<?php

require '../classes/db.php';

$errors = array();
// database connection

$db = db::getInstance();

if(isset($_GET['user'])) {
    $username = $_GET['user'];

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

if (isset($_GET['id'])) {
    unset($topicId);
    $topicId = $_GET['id'];
    $topics = $db->__getTopics("id = '$topicId'", 1);
    if ($topics) {
        $topicTitle = $topics[0]->title;
    } else {
        array_push($errors, 'no topics found');
    } 
    
    if (count($errors) == 0) {
        $entryQuery = "SELECT * FROM entries WHERE topicId = '$topicId'";
        $entries = $db->__getEntries("topicId='$topicId'");
        if ($entries == null) {
            array_push($errors, "No entries found!");

        } else {
            echo "
                <h1>$topicTitle</h1>
                <div class='entries'>"; 
            foreach ($entries as &$entry) {
                $entry->__toString();

                if ($username == $entry->createdBy || $userType == 'Admin')  {
                    $eId = $entry->id;
                    echo "<a href='server.php?deleteId=$eId'>Delete</a>";
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
