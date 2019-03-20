<form action="search.php" method="get">
    <label for="search">What are you looking for?</label>
    <input type="search" name="search">
    <input type="submit" value="search">
</form>
<?php

$errors = array();
$topicErrors = array();
$entryErrors = array();

$minStringLength = 3;


include '../classes/db.php';

// database connection
$db = db::getInstance();


if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    // find user type
    $users = $db->__getUsers("username='$username'", 1);
    if ($users) {
        $userType = $users[0]->type;
    } else {
        echo "no users found";
    }
} else {
    $username = '';
    $userType = '';
}

if (isset($_GET['search'])) {
    unset($search, $tQuery, $tResult, $tNumRows);
    $search = $_GET['search'];

    if (strlen($search) >= $minStringLength) {
        $search = htmlspecialchars($search);
        $search = $db->escape_string($search);

        $topics = $db->__searchDb("topics", "(t.title, t.description)", $search);

       if ($topics == null) {
           array_push($topicErrors, "no topics found with: '$search'");
       } else {
            echo '<p>In <b>Topics</b> your search found this: </p><div>';  
            foreach ($topics as &$topic) {
                $topic->__toString();
                if ($username != '') {
                    echo "<a href='./showEntries.php?id=$topic->id&user=$username'>Show All Entries($topic->entryCount)</a>";
                } else {
                    echo "<a href='./showEntries.php?id=$topic->id'>Show All Entries($topic->entryCount)</a>";
                }
           }
           echo '</div>';
       }

       $entries = $db->__searchDb("entries", "(title, content)", $search);

       if ($entries == null) {
            array_push($entryErrors, "no entries found with: '$search'");
       } else {
            echo '<p>In <b>Entries</b> your search found this: </p><div>';  
            foreach ($entries as &$entry) {
                $entry->__toString();
                $eId = $entry->id;
                $tId = $entry->topicId;
                $entryTopics = $db->__getTopics("id=$tId", 1);
                $topicTitle = $entryTopics[0]->title;
                if ($username == $entry->createdBy || $userType == 'Admin')  {
                    echo "<a href='server.php?deleteId=$eId'>Delete</a>";
                }
                echo "
                    <p>This entry is under the $topicTitle topic<p>
                    <a href='./showEntries.php?id=$tId'>show all entries under the $topicTitle topic</a>";
            }
            echo '</div>';
       }
       echo '<br><a href="../index.php">back</a>';
    } else {
        array_push($topicErrors, "search too short");
    }
    if (sizeof($topicErrors) > 0 || sizeof($entryErrors) > 0 && sizeof($errors) == 0) {
        $errors = array_merge($entryErrors, $topicErrors);
    }
    
    include('./errors.php');
      /*   
        $eQuery = "SELECT * FROM entries WHERE MATCH (title, content) AGAINST ('*$search*' IN BOOLEAN MODE)";
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
            echo '<br><a href="../index.php">back</a>';
            echo '</div>';
        }
    }
    else {
        array_push($errors, 'searchword must be longer that 3 characters');
    }*/
} 

?>