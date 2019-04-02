<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap-theme.min.css" crossorigin="anonymous">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" crossorigin="anonymous"></script>
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Topic</title>
</head>
<body>
    <div class="container-fluid mt-3">

</body>
</html>


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
        echo "<h1>$topicTitle</h1>";
        if ($entries == null) {
            array_push($errors, "No entries found!");

        } else {
            echo "<div class='mt-3 p-3 border bg-light'>"; 
            foreach ($entries as &$entry) {
                $entry->__toString();

                if ($username == $entry->createdBy || $userType == 'Admin')  {
                    $eId = $entry->id;
                    echo "<a class='btn btn-danger' href='server.php?deleteId=$eId'>Delete</a>";
                }
            }
            echo "</div>";
        }
    }
    echo "
    <div class='mt-3'>
        <a class='btn btn-primary' href='./create.php?topic=1'>Create Topic</a>
        <a class='btn btn-primary' href='./create.php?topic=0'>Write entry</a>
        <a class='btn btn-danger' href='../index.php'>&lt;back</a>
    </div>
    ";
}
echo '</div>';
$db->close();

?>
