<?php

require 'db.php';

$errors = array();
// database connection
$db = db::getInstance();

$popularityQuery = "SELECT t.*, count(e.topicId) as entryCount FROM topics t LEFT OUTER JOIN entries e ON t.id = e.topicId GROUP BY t.id ORDER BY COUNT(e.topicId) DESC";
$chronologicalQuery = "SELECT t.*, count(e.topicId) as entryCount FROM topics t LEFT OUTER JOIN entries e ON t.id = e.topicId GROUP BY t.id";
$topicQuery = '';

if(isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    if (isset($_COOKIE['sorting'.$username.''])) {
        $sortMethod = $_COOKIE['sorting'.$username.''];
        $sortMethod == 1 ? $topicQuery = $popularityQuery : $topicQuery = $chronologicalQuery;
    } else {
        $topicQuery = $chronologicalQuery;
    }
} else if (!isset($_SESSION['username']) && isset($_SESSION['sortingMethod']) ){
    $sortMethod = $_SESSION['sortingMethod'];
    $sortMethod == 1 ? $topicQuery = $popularityQuery : $topicQuery = $chronologicalQuery;
    $username = '';
} else {
    $topicQuery = $chronologicalQuery;
    $username = '';
}

$topicResult = $db->query($topicQuery);

$numberRows = $db->get_rows($topicResult);

if ($numberRows == 0) {
    array_push($errors, 'no entries found');
} else {
    echo "
        <div>
        <h2>Topics</h2>";
    while ($row = $topicResult->fetch_assoc()) {
        unset($tId, $title, $description, $createdBy);
        
        $userId = $row["createdBy"];
        $userQuery = "SELECT id, username FROM users WHERE id='$userId' LIMIT 1";
        $userResult = $db->get_result($userQuery);

        if ($userResult['id'] == $userId) {
            $createdBy = $userResult['username'];
        } else {
            array_push($errors, 'did not find user');
        }
        $tId = $row['id'];
        $title = $row['title']; 
        $description = $row['description']; 
        if (count($errors) == 0) {
        echo "
            <h3>$title</h3>
            <p>$description</p>
            <span>created By: $createdBy</span><br>
            <a href='./src/showEntries.php?id=$tId'>Show All Entries</a>
            <h4>Entries: </h4>";
        }

        $entryQuery = "SELECT * FROM entries WHERE topicId='$tId' LIMIT 1";
        $entryResult = $db->query($entryQuery);

        $numRows = $db->get_rows($entryResult);
        if ($numRows == 0) {
            array_push($errors, 'no entries found'); 
            if ($username == $createdBy) {
                echo "<a href='./src/server.php?deleteTopicId=$tId&user=$createdBy'>Delete topic</a>";
            }
        } else {
            echo "<div class='entry'>";
            while ($eRow = $entryResult->fetch_assoc()) {
                unset($eId, $eTitle, $content, $eCreatedBy);
                
                $userId = $eRow["createdBy"];
                $userQuery = "SELECT id, username FROM users WHERE id='$userId' LIMIT 1";
                $userResult = $db->get_result($userQuery);
                $eId = $eRow['id'];
                if ($userId == $userResult['id']) {
                    $eCreatedBy = $userResult['username'];
                } else {
                    array_push($errors, 'did not find user');
                }

                $eTitle = $eRow['title']; 
                $content = $eRow['content']; 

                if (count($errors) == 0) {
                    echo "
                        <b>$eTitle</b>
                        <p>$content</p>
                        <span>entry created by: $eCreatedBy</span>
                        ";
                    if ($username == $eCreatedBy ) {
                        echo "<a href='./src/server.php?deleteId=$eId'>Delete</a>";
                    }
                }
            }
            echo '</div>';
        }
    }
    echo "</div>";
}
$db->close();

?>
