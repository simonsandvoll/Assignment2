<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap-theme.min.css" crossorigin="anonymous">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" crossorigin="anonymous"></script>
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Users</title>
</head>
<body>
    <div class="container-fluid mt-3">

</body>
</html>



<?php
$username = "";

require '../classes/sessionClass.php';
require_once '../classes/db.php';

$db = db::getInstance();
$session = new Session($db);
$session->__start();

$errors = array();

// REGISTER USER___________________________________________________________________________________
if (isset($_POST['regUser'])) {

    $username = $_POST['username'];
    $password_1 = $_POST['password_1'];
    $password_2 = $_POST['password_2'];
    
    // check if user exists in database
    unset($user);
    $user = $db->__getUsers("username='$username'", 1);
    if ($user) { array_push($errors, "Username already exists"); } 
    else {
        // validate form
        $valid = $session->__validateUserData($username, $password_1, $password_2);
        if ($valid[0] === false) { array_push($errors, $valid[1]); }
        if (count($errors) == 0) {
            $password = $password_1;
            $session->__register($username, $password);
            $session->__setSession('username', $username);
            $session->__setSession('usertype', 'Author');
            $session->__setSession('success', "You are now logged in!");
            header('location: ../index.php');
        }
    }
}
// !____________________________________________________________________________________________

// LOGIN USER___________________________________________________________________________________
if (isset($_POST['loginUser'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $valid = $session->__validateLoginData($username, $password);
    if ($valid[0] === false) { array_push($errors, $valid[1]); }
    if (count($errors) == 0) {
        $username = $db->escape_string($username);
        $password = $db->escape_string($password);
        $password = md5($password); // encrypt password 
        $users = $db->__getUsers("username='$username' AND password='$password'", 1);
        if ($users) {
            $user = $users[0];
            $session->__setSession('username', $user->username);
            $session->__setSession('usertype', $user->type);
            $session->__setSession('success', "You are now logged in!");
            header('location: ../index.php');
        } else { array_push($errors, "wrong username and password combination"); }
    }   
}
// !_____________________________________________________________________________________________

// CREATE TOPIC__________________________________________________________________________________
if (isset($_POST['createTopic'])) {
    if ($session->__getSession('username') === null) {
        array_push($errors, 'must be logged in to create topics');
    } else {

        // escape special characters in a string for use in an SQL statement
        $title = $db->escape_string($_POST['title']);
        $description = $db->escape_string($_POST['description']);
    
        $username = $session->__getSession('username');
        
        // get user id from session
        $createdBy = $session->__getCreatedBy($username);
        if ($createdBy === null) { array_push($errors, 'invalid username'); }
    
        // validate form
        $valid = $session->__validateTopicData($title, $description);
        if ($valid[0] === false) { array_push($errors, $valid[1]); }
        // insert topic into database
        if (count($errors) == 0) {
            $session->__insertInto('topics', 
                    '(title, description, createdBy)', 
                    "('$title', '$description', '$createdBy')");
            $session->__setSession('success', "Created topic");
            header('location: ../index.php');
        }
    }
}
// !_____________________________________________________________________________________________

// CREATE ENTRY__________________________________________________________________________________

if (isset($_POST['writeEntry'])) {
    if ($session->__getSession('username') === null) {
        array_push($errors, 'must be logged in to write entries');
    } else {
        $username = $session->__getSession('username');
    
        // escape special characters in a string for use in an SQL statement
        $title = $db->escape_string($_POST['title']);
        $content = $db->escape_string($_POST['content']);
        $topicId = $db->escape_string($_POST['topic']);
    
        // get user id from session
        $createdBy = $session->__getCreatedBy($username);
        if ($createdBy === null) { array_push($errors, 'invalid username'); }
    
        // get topic id from session
        $tId = $session->__getTopicInfo($topicId);
        if ($tId === null) { array_push($errors, 'invalid topic'); }
    
        // validate form
        $valid = $session->__validateEntryData($title, $content, $topicId);
        if ($valid[0] === false) { array_push($errors, $valid[1]); }
        // insert entry into database
        if (count($errors) == 0) {
            $session->__insertInto('entries', 
                    '(title, content, createdBy, topicId)', 
                    "('$title', '$content', '$createdBy', '$tId')");
            $session->__setSession('success', "Created entry");
            header('location: ../index.php');
        }
    } 
}
// !_____________________________________________________________________________________________

// DELETE ENTRY__________________________________________________________________________________
if (isset($_GET['deleteId'])) {
    if ($session->__getSession('username') === null) {
        array_push($errors, 'must be logged in to delete entries');
    } else {
        unset($deleteId, $username);
        $username = $session->__getSession('username');
        $deleteId = $_GET['deleteId'];
        $userId = '';
        // get user id from session
        $userId = $session->__getCreatedBy($username);
        if ($userId === null) { array_push($errors, 'invalid username'); }
    
        if (count($errors) == 0) {
            // delete entry
            $entryResult = $session->__delete('entries', $deleteId);
            $session->__setSession("success", "Entry deleted");
            header('location: ../index.php');
        }
    }
}
// !_____________________________________________________________________________________________

// DELETE TOPIC__________________________________________________________________________________
if (isset($_GET['deleteTopicId'])) {
    if ($session->__getSession('username') === null) {
        array_push($errors, 'must be logged in to delete topics');
    } else {
        unset($deleteId, $username);
        $username = $session->__getSession('username');
        $deleteId = $_GET['deleteTopicId'];
        $userId = '';

        // get user id from session
        $userId = $session->__getCreatedBy($username);
        if ($userId === null) { array_push($errors, 'invalid username'); }
    
        if (count($errors) == 0) {
            // delete topic
            $entryResult = $session->__delete('topics', $deleteId);
            $session->__setSession("success", "Topic deleted");
            header("location: ../index.php");
        }
    }
}
// !_____________________________________________________________________________________________

// DELETE USER___________________________________________________________________________________

if (isset($_GET['deleteUserId'])) {
    $username = $session->__getSession('username');
    $userType = $session->__getSession('usertype');
    if ($username === null || $userType != 'Admin') {
        array_push($errors, 'Invalid user information');
    } else {
        $deleteId = $_GET['deleteUserId'];
        if (isset($_GET['delete'])) {
            $deleteConfirmation = $_GET['delete'];
            if ($deleteConfirmation === 'Yes') {
                $session->__delete('users', $deleteId);
                $session->__setSession('success', "User deleted");
                header('location: ../index.php');
            } else {
                header("location: showUsers.php?user=$username");
            }
        } else {
            // check if user has entries that he/she has created
            $entries = $db->__getEntries("createdBy=$deleteId");
            $topics = $db->__getTopics("createdBy=$deleteId");
            if ($entries != null && $topics !=null) {
                echo "<div>
                    <p>this user <b> has </b> entries and topics that he/she has created</p>
                    <p>Are you sure you want to delete this user and all his/her entries and topics</p>
                    <a class='btn btn-danger' href='../src/server.php?deleteUserId=$deleteId&delete=Yes'>Yes</a>
                    <a class='btn btn-primary' href='../src/server.php?deleteUserId=$deleteId&delete=No'>No</a></div>
                ";
            } else if($entries == null && $topics != null) {
                echo "<div>
                    <p>this user has <b> no </b> entries, but has topics that he/she has created</p>
                    <p>Are you sure you want to delete this user and his/her topics?</p>
                    <a class='btn btn-danger' href='../src/server.php?deleteUserId=$deleteId&delete=Yes'>Yes</a>
                    <a class='btn btn-primary' href='../src/server.php?deleteUserId=$deleteId&delete=No'>No</a></div>
                ";
            } else if($entries != null && $topics == null) {
                echo "<div>
                    <p>this user has <b> no </b> topics, but has entries that he/she has created</p>
                    <p>Are you sure you want to delete this user and his/her entries?</p>
                    <a class='btn btn-danger' href='../src/server.php?deleteUserId=$deleteId&delete=Yes'>Yes</a>
                    <a class='btn btn-primary' href='../src/server.php?deleteUserId=$deleteId&delete=No'>No</a></div>
                ";
            }  else if($entries == null && $topics == null) {
                echo "<div>
                    <p>this user has <b> no </b> entries or topics that he/she has created</p>
                    <p>Are you sure you want to delete this user?</p>
                    <a class='btn btn-danger' href='../src/server.php?deleteUserId=$deleteId&delete=Yes'>Yes</a>
                    <a  class='btn btn-primary'href='../src/server.php?deleteUserId=$deleteId&delete=No'>No</a></div>
                ";
            }
        } 
    }  
}
// !_____________________________________________________________________________________________

// SORT TOPICS___________________________________________________________________________________
if (isset($_GET['sortingMethod']) && isset($_SESSION['username'])) {
    $sortingMethod = $_GET['sortingMethod'];
    $username = $session->__getSession('username');
    setcookie('sorting'. $username . '', $sortingMethod, time() + (86400 * 30), "/");
    header('location: ../index.php');
} else if (isset($_GET['sortingMethod']) && !isset($_SESSION['username'])) {
    $sortingMethod = $_GET['sortingMethod'];
    $_SESSION['sortingMethod'] = $sortingMethod;
    header('location: ../index.php');
}
// !_____________________________________________________________________________________________
echo '</div>';
$db->close();
?>