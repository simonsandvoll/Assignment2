<?php

session_start();

require '../classes/db.php';

$username = "";
$errors = array();

// database connection
$db = db::getInstance();

// REGISTER USER___________________________________________________________________________________
if (isset($_POST['regUser'])) {
   
    $username = $db->escape_string($_POST['username']);
    $password_1 = $db->escape_string($_POST['password_1']);
    $password_2 = $db->escape_string($_POST['password_2']);
    
    // validate form
    if (empty($username)) { array_push($errors, "username is requried"); }
    if (empty($password_1)) { array_push($errors, "password is required"); }
    if ($password_1 != $password_2) { array_push($errors, "The two passwords do not match"); }

    // check if user exists in database
    $user = $db->__getUsers("username='$username'", 1);
    if ($user) {
        array_push($errors, "Username already exists");
    } else {
        echo 'user does not exist';
        $password = md5($password_1); // encrypt password 

        $insertUserQuery = "INSERT INTO users (username, password) VALUES ('$username', '$password')";
        $db->dbquery($insertUserQuery);
        $_SESSION['username'] = $username;
        $_SESSION['success'] = "You are now logged in";
        header('location: ../index.php');
    }
}
// !____________________________________________________________________________________________

// LOGIN USER___________________________________________________________________________________
if (isset($_POST['loginUser'])) {

    $username = $db->escape_string($_POST['username']);
    $password = $db->escape_string($_POST['password']);

    if (empty($username)) {
        array_push($errors, "Username is required");
    }
    if (empty($password)) {
        array_push($errors, "Password is required");
    }

    if (count($errors) == 0) {
        $password = md5($password);
        $users = $db->__getUsers("username='$username' AND password='$password'", 1);

        if ($users) {
            $user = $users[0];
            $_SESSION['username'] = $user->username;
            $_SESSION['userType'] = $user->type;
            $_SESSION['success'] = "You are now logged in";
            header('location: ../index.php'); 
        } else {
            array_push($errors, "Wrong username/password combination");
        }
    }
}
// !_____________________________________________________________________________________________

// CREATE TOPIC__________________________________________________________________________________
if (isset($_POST['createTopic'])) {
    if (isset($_SESSION['username'])) {
        $username = $_SESSION['username'];
        
        // get user from database
        $users = $db->__getUsers("username='$username'", 1);
        if ($users) {
            $user = $users[0];
            $createdBy = $user->id;
        } else {
            array_push($errors, "Wrong username");
        }

        // escape special characters in a string for use in an SQL statement
        $title = $db->escape_string($_POST['title']);
        $description = $db->escape_string($_POST['description']);

        // validate form
        if (empty($title)) { array_push($errors, "title is requried"); }
        if (empty($description)) { array_push($errors, "description is requried"); }

        if (count($errors) == 0) {
            $query = "INSERT INTO topics (title, description, createdBy)
                    VALUES('$title', '$description', '$createdBy')";
            $db->dbquery($query);
            $_SESSION['success'] = "Created topic";
            header('location: ../index.php');
        }
    } else {
        array_push($errors, 'must be logged in to create topics');
    }
}
// !_____________________________________________________________________________________________

// CREATE ENTRY__________________________________________________________________________________

if (isset($_POST['writeEntry'])) {
    if (isset($_SESSION['username'])) {
        $username = $_SESSION['username'];

        // escape special characters in a string for use in an SQL statement
        $title = $db->escape_string($_POST['title']);
        $content = $db->escape_string($_POST['content']);
        $topicId = $db->escape_string($_POST['topic']);

        // validate form
        if (empty($title)) { array_push($errors, "title is requried"); }
        if (empty($content)) { array_push($errors, "content is requried"); }
        if (empty($topicId)) { array_push($errors, "topic is requried"); }

        echo $topicId;
        // get user from database
        $users = $db->__getUsers("username='$username'", 1);
        if ($users) {
            $user = $users[0];
            $createdBy = $user->id;
        } else {
            array_push($errors, "Wrong username");
        }

        // check topics from database
        $topics = $db->__getTopics("id='$topicId'", 1);
        if ($topics) {
            $topic = $topics[0];
            $tId = $topic->id;
        } else {
            array_push($errors, "Topic does not exist");
        }
        
        if (count($errors) == 0) {
            $query = "INSERT INTO entries (title, content, createdBy, topicId)
                    VALUES('$title', '$content', '$createdBy', '$tId')";
            $db->dbquery($query);
            $_SESSION['success'] = "Written entry";
            header('location: ../index.php');
        }
    } else {
        array_push($errors, 'must be logged in to write entries');
    }
}
// !_____________________________________________________________________________________________

// DELETE ENTRY__________________________________________________________________________________
if (isset($_GET['deleteId'])) {
    if (isset($_SESSION['username'])) {
        unset($deleteId, $username, $userId);
        
        $deleteId = $_GET['deleteId'];
        $username = $_SESSION['username'];
        $userId = '';
        
        // check if user is in database
        $users = $db->__getUsers("username='$username'", 1);
        if ($users) {
            $user = $users[0];
            $userId = $user->id;
        } else {
            array_push($errors, "Wrong username");
        }

        if (count($errors) == 0) {
            // delete entry
            $entryQuery = "DELETE FROM entries WHERE id='$deleteId'";
            $entryResult = $db->dbquery($entryQuery);
            if ($entryResult) {
                $_SESSION['success'] = "Entry deleted";
                header('location: ../index.php');
            } else {
                array_push($errors, "Error deleting entry");
            }
        }   
    }
}
// !_____________________________________________________________________________________________

// DELETE TOPIC__________________________________________________________________________________
if (isset($_GET['deleteTopicId'])) {
    if (isset($_SESSION['username'])) {
        unset($deleteId, $username, $userId);
        $deleteId = $_GET['deleteTopicId'];
        $username = $_SESSION['username'];
        $userId = '';

        // check if user is in database
        $users = $db->__getUsers("username='$username'", 1);
        if ($users) {
            $user = $users[0];
            $userId = $user->id;
        } else {
            array_push($errors, "Wrong username");
        }

        if (count($errors) == 0) {
            // delete topic
            $topicQuery = "DELETE FROM topics WHERE id='$deleteId'";
            $topicResult = $db->dbquery($topicQuery);
            if ($topicResult) {
                $_SESSION['success'] = "Topic deleted";
                header('location: ../index.php');
            } else {
                array_push($errors, "Error deleting topic");
            }
        }
    }
}
// !_____________________________________________________________________________________________

// DELETE USER___________________________________________________________________________________

if (isset($_GET['deleteUserId']) && isset($_SESSION['username']) && isset($_SESSION['userType'])) {
    unset($deleteId, $username, $userType, $deleteType);
    $deleteId = $_GET['deleteUserId'];
    $username = $_SESSION['username'];
    $userType = $_SESSION['userType'];
    if (isset($_GET['delete']) && $userType == 'Admin') {
        $deleteType = $_GET['delete'];
        if ($deleteType == 'Yes') {
            $query = "DELETE FROM users WHERE id='$deleteId'";
            $result = $db->dbquery($query);
            if($result) {
                $_SESSION['success'] = "User deleted";
                header('location: ../index.php');
            } else {
                array_push($errors, "Error deleting user");
            }
        }
    } else {
        if ($userType == 'Admin') {
            // check if user has entries that he has created
            $entries = $db->__getEntries("createdBy=$deleteId");
            $topics = $db->__getTopics("createdBy=$deleteId");
            if ($entries != null && $topics !=null) {
                echo "
                    <p>this user <b> has </b> entries and topics that he/she has created</p>
                    <p>Are you sure you want to delete this user and all his/her entries and topics</p>
                    <a href='../src/server.php?deleteUserId=$deleteId&delete=Yes'>Yes</a>
                    <a href='../src/server.php?deleteUserId=$deleteId&delete=No'>No</a>
                ";
            } else if($entries == null && $topics != null) {
                echo "
                    <p>this user has <b> no </b> entries, but has topics that he/she has created</p>
                    <p>Are you sure you want to delete this user and his/her topics?</p>
                    <a href='../src/server.php?deleteUserId=$deleteId&delete=Yes'>Yes</a>
                    <a href='../src/server.php?deleteUserId=$deleteId&delete=No'>No</a>
                ";
            } else if($entries != null && $topics == null) {
                echo "
                    <p>this user has <b> no </b> topics, but has entries that he/she has created</p>
                    <p>Are you sure you want to delete this user and his/her entries?</p>
                    <a href='../src/server.php?deleteUserId=$deleteId&delete=Yes'>Yes</a>
                    <a href='../src/server.php?deleteUserId=$deleteId&delete=No'>No</a>
                ";
            }  else if($entries == null && $topics == null) {
                echo "
                    <p>this user has <b> no </b> entries or topics that he/she has created</p>
                    <p>Are you sure you want to delete this user?</p>
                    <a href='../src/server.php?deleteUserId=$deleteId&delete=Yes'>Yes</a>
                    <a href='../src/server.php?deleteUserId=$deleteId&delete=No'>No</a>
                ";
            }
        }
    }
}

// !_____________________________________________________________________________________________

// SORT TOPICS___________________________________________________________________________________
if (isset($_GET['sortingMethod']) && isset($_SESSION['username'])) {
    $sortingMethod = $_GET['sortingMethod'];
    $username = $_SESSION['username'];
    setcookie('sorting'. $username . '', $sortingMethod, time() + (86400 * 30), "/");
    header('location: ../index.php');
} else if (isset($_GET['sortingMethod']) && !isset($_SESSION['username'])) {
    $sortingMethod = $_GET['sortingMethod'];
    $_SESSION['sortingMethod'] = $sortingMethod;
    header('location: ../index.php');
}
// !_____________________________________________________________________________________________

$db->close();
?>

