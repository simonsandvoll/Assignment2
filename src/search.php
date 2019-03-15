<?php

$errors = array();
$topicErrors = array();
$entryErrors = array();

$minStringLength = 3;
if (isset($_GET['search'])) {
    unset($search, $tQuery, $tResult, $tNumRows);
    $search = $_GET['search'];

    // database connection
    $db = mysqli_connect('localhost', 'root', '', 'urbandictionary');

    if (strlen($search) >= $minStringLength) {
        $search = htmlspecialchars($search);
        $search = mysqli_real_escape_string($db, $search);
        $tQuery = "SELECT * FROM topics WHERE MATCH (title, description) AGAINST ('$search' IN BOOLEAN MODE)";
        $tResult = mysqli_query($db, $tQuery);
        if (!$tResult) { $tNumRows = 0; } else {
            $tNumRows = mysqli_num_rows($tResult);
        }
        if ($tNumRows == 0) {
            array_push($topicErrors, "no topics found with: '$search'");
        } else {
            echo '<b>in topics your search found: </b><div>';
            while ($tRow = $tResult->fetch_assoc()) {
                unset($topicTitle, $topicDesc, $topicCreatedBy);
                
                $topicUserId = $tRow["createdBy"];
                $topicUserQuery = "SELECT username FROM users WHERE id='$topicUserId'";
                $topicUserResult = mysqli_query($db, $topicUserQuery);
                $topicUsers = mysqli_fetch_assoc($topicUserResult);

                if (mysqli_num_rows($topicUserResult) == 1) {
                    $topicCreatedBy = $topicUsers['username'];
                } else {
                    array_push($topicErrors, 'did not find user');
                }
                $topicTitle = $tRow['title']; 
                $topicDesc = $tRow['description']; 
                if (count($topicErrors) == 0) {
                echo "
                    <h2>$topicTitle</h2>
                    <p>$topicDesc<p>
                    <span>topic created by: $topicCreatedBy</span>
                    ";
                }
            }
            echo '</div>';
        }
        $eQuery = "SELECT * FROM entries WHERE MATCH (title, content) AGAINST ('$search' IN BOOLEAN MODE)";
        $eResult = mysqli_query($db, $eQuery);

        if (!$eResult) { $eNumRows = 0; } else {
            $eNumRows = mysqli_num_rows($eResult);
        }
        if ($eNumRows == 0) {
            array_push($entryErrors, "no entries found with: '$search'");
        } else {
            echo '<b>in entries your search found: </b><div>';
            while ($eRow = $eResult->fetch_assoc()) {
                unset($entryTitle, $entryContent, $entryCreatedBy, $tId);
                $entryUserId = $eRow["createdBy"];
                $entryUserQuery = "SELECT username FROM users WHERE id='$entryUserId'";
                $entryUserResult = mysqli_query($db, $entryUserQuery);
                $entryUsers = mysqli_fetch_assoc($entryUserResult);

                if (mysqli_num_rows($entryUserResult) == 1) {
                    $entryCreatedBy = $entryUsers['username'];
                } else {
                    array_push($entryErrors, 'did not find user');
                }

                $entryTopicId = $eRow['topicId'];
                $entryTopicQuery = "SELECT title FROM topics WHERE id='$entryTopicId'";
                $entryTopicResult = mysqli_query($db, $entryTopicQuery);
                $entryTopic = mysqli_fetch_assoc($entryTopicResult);

                if (mysqli_num_rows($entryTopicResult) == 1) {
                    $tId = $entryTopic['title'];
                } else {
                    array_push($entryErrors, 'did not find topic');
                }

                $entryTitle = $eRow['title']; 
                $entryContent = $eRow['content']; 
                if (count($entryErrors) == 0) {
                echo "
                    <h2>$entryTitle</h2>
                    <p>$entryContent<p>
                    <span>entry written by: $entryCreatedBy</span><br>
                    <p>This entry is under the $tId topic<p>
                    <a href='./showEntries.php?id=$entryTopicId'>show all entries under the $tId topic</a>
                    ";
                }
            }
            echo '</div>';
        }
    }
}
if ($topicErrors <= 0 || $entryErrors <= 0) {
    $errors = array_slice($entryErrors, 0, sizeof($entryErrors)-1);
    $errors = array_slice($topicErrors, 0, sizeof($topicErrors)-1);
}
include('./errors.php');
?>