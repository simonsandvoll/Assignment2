<?php

require 'db.php';

if (isset($_GET['user'])) {
    $username = $_GET['user'];
} else {
    $username = '';
}

$errors = array();
// database connection

$db = db::getInstance();

if (isset($_GET['id'])) {
    unset($topicId);
    $topicId = $_GET['id'];
    $topicQuery = "SELECT id, title FROM topics WHERE id = '$topicId' LIMIT 1";
    $topicResult = $db->get_result($topicQuery);
    if ($topicId == $topicResult['id']) {
        $topicTitle = $topicResult['title'];
    } else {
        array_push($errors, 'no topics found');
    } 
    
    if (count($errors) == 0) {
        $entryQuery = "SELECT * FROM entries WHERE topicId = '$topicId'";
        $entryResult = $db->query($entryQuery);

        $numRows = $db->get_rows($entryResult);
        if ($numRows == 0) {
            array_push($errors, 'no entries found');
        } else {
            echo "
                <h1>$topicTitle</h1>
                <div class='entries'>";
            while ($row = $entryResult->fetch_assoc()) {
                unset($title, $content, $createdBy, $topic);
                
                $userId = $row["createdBy"];
                $userQuery = "SELECT id, username FROM users WHERE id='$userId' LIMIT 1";
                $userResult = $db->get_result($userQuery);

                if ($userId == $userResult['id']) {
                    $createdBy = $userResult['username'];
                } else {
                    array_push($errors, 'did not find user');
                }

                $title = $row['title']; 
                $content = $row['content']; 
                if (count($errors) == 0) {
                $eId = $row['id'];
                echo "
                    <h3>$title</h3>
                    <p>$content<p>
                    <span>entry written by: $createdBy</span>
                    <p>under the $topicTitle topic</p>";
                    if ($username == $createdBy) {
                        echo "<a href='server.php?deleteId=$eId'>Delete</a>";
                    }
                }
            }
            echo "
                </div>
                <a href='./create.php?topic=1'>Create Topic</a>
                <a href='./create.php?topic=0'>Write entry</a>
                <a href='../index.php'>Back</a>
                ";
        }
    }
}
$db->close();

?>
