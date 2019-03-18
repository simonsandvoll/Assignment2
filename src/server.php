<?php

session_start();

require 'db.php';

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
    $checkQuery = "SELECT * FROM users WHERE username='$username' LIMIT 1";
    $user = $db->get_result($checkQuery);

    if ($user) {
        if ($user['username'] === $username) {
            array_push($errors, "Username already exists");
        }
    }
    if (count($errors) == 0) {
        $password = md5($password_1); // encrypt password 

        $insertUserQuery = "INSERT INTO users (username, password) VALUES('$username', '$password')";
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
        $query = "SELECT * FROM users WHERE username='$username' and password='$password'";
        $result = $db->get_result($query);
        if (mysqli_num_rows($result) == 1) {
            $_SESSION['username'] = $username;
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
        $query = "SELECT id FROM users WHERE username='$username' LIMIT 1";
        $result = mysqli_query($db, $query);
        $user = mysqli_fetch_assoc($result);
        if (mysqli_num_rows($result) == 1) {
            $createdBy = $user['id'];
        } else {
            array_push($errors, "Wrong username combination");
        }

        $title = $db->escape_string($_POST['title']);
        $description = mysqli_real_escape_string($db, $_POST['description']);

        // validate form
        if (empty($title)) { array_push($errors, "title is requried"); }

        if (count($errors) == 0) {
            $query = "INSERT INTO topics (title, description, createdBy)
                    VALUES('$title', '$description', '$createdBy')";
            mysqli_query($db, $query);
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

        // get user from database
        $query = "SELECT id FROM users WHERE username='$username' LIMIT 1";
        $result = mysqli_query($db, $query);
        $user = mysqli_fetch_assoc($result);
        if (mysqli_num_rows($result) == 1) {
            $createdBy = $user['id'];
        } else {
            array_push($errors, "Wrong username");
        }

        $title = mysqli_real_escape_string($db, $_POST['title']);
        $content = mysqli_real_escape_string($db, $_POST['content']);
        $topic = mysqli_real_escape_string($db, $_POST['topic']);

        // check topics from database
        $query = "SELECT id FROM topics WHERE id='$topic' LIMIT 1";
        $result = mysqli_query($db, $query);
        $topicQuery = mysqli_fetch_assoc($result);
        if (mysqli_num_rows($result) == 1) {
            $topic = $topicQuery['id'];
        } else {
            array_push($errors, "Topic does not exist");
        }

        // validate form
        if (empty($title)) { array_push($errors, "title is requried"); }
        if (empty($content)) { array_push($errors, "content is requried"); }
        if (empty($topic)) { array_push($errors, "topic is requried"); }

        if (count($errors) == 0) {
            $query = "INSERT INTO entries (title, content, createdBy, topicId)
                    VALUES('$title', '$content', '$createdBy', '$topic')";
            mysqli_query($db, $query);
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
        // get user from database
        $uQuery = "SELECT id FROM users WHERE username='$username' LIMIT 1";
        $eResult = mysqli_query($db, $uQuery);
        $user = mysqli_fetch_assoc($eResult);
        if (mysqli_num_rows($eResult) == 1) {
            $userId = $user['id'];
        } else {
            array_push($errors, "Wrong username");
        }

        if (count($errors) == 0) {
            // delete entry
            $entryQuery = "DELETE FROM entries WHERE createdBy='$userId' AND id='$deleteId'";
            $entryResult = mysqli_query($db, $entryQuery);
            if ($entryResult) {
                echo 'deleted entry ' . $deleteId . ' by ' . $username;
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
        $uQuery = "SELECT id FROM users WHERE username='$username' LIMIT 1";
        $eResult = mysqli_query($db, $uQuery);
        $user = mysqli_fetch_assoc($eResult);
        if (mysqli_num_rows($eResult) == 1) {
            $userId = $user['id'];
        } else {
            array_push($errors, "Wrong username");
        }

        if (count($errors) == 0) {
            // delete topic
            $entryQuery = "DELETE FROM topics WHERE createdBy='$userId' AND id='$deleteId'";
            $entryResult = mysqli_query($db, $entryQuery);
            if ($entryResult) {
                echo 'deleted topic ' . $deleteId . ' by ' . $username;
                $_SESSION['success'] = "Topic deleted";
                header('location: ../index.php');
            } else {
                array_push($errors, "Error deleting topic");
            }
        }
    }
}
// !_____________________________________________________________________________________________
// SORT TOPICS
if (isset($_GET['sortingMethod']) && $_SESSION['username']) {
    $sortingMethod = $_GET['sortingMethod'];
    $username = $_SESSION['username'];
    setcookie('sorting'. $username . '', $sortingMethod, time() + (86400 * 30), "/");
    header('location: ../index.php');
}
?>

