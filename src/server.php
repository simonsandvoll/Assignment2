<?php

session_start();

$username = "";
$errors = array();

// database connection
$db = mysqli_connect('localhost', 'root', '', 'urbandictionary');

// REGISTER USER__________________________________________________________
if (isset($_POST['regUser'])) {
    $username = mysqli_real_escape_string($db, $_POST['username']);
    $password_1 = mysqli_real_escape_string($db, $_POST['password_1']);
    $password_2 = mysqli_real_escape_string($db, $_POST['password_2']);
    
    // validate form
    if (empty($username)) { array_push($errors, "username is requried"); }
    if (empty($password_1)) { array_push($errors, "password is required"); }
    if ($password_1 != $password_2) { array_push($errors, "The two passowrds do not match"); }

    // check if user exists in database
    $checkQuery = "SELECT * FROM users WHERE username='$username' LIMIT 1";
    $result = mysqli_query($db, $checkQuery);
    $user = mysqli_fetch_assoc($result);

    if ($user) {
        if ($user['username'] === $username) {
            array_push($errors, "Username already exists");
        }
    }
    if (count($errors) == 0) {
        $password = md5($password_1); // encrypt password 

        $query = "INSERT INTO users (username, password)
                  VALUES('$username', '$password')";
        mysqli_query($db, $query);
        $_SESSION['username'] = $username;
        $_SESSION['success'] = "You are now logged in";
        header('location: ../index.php');
    }
}
// !_______________________________

// LOGIN USER__________________________________________________________
if (isset($_POST['loginUser'])) {
    $username = mysqli_real_escape_string($db, $_POST['username']);
    $password = mysqli_real_escape_string($db, $_POST['password']);

    if (empty($username)) {
        array_push($errors, "Username is required");
    }
    if (empty($password)) {
        array_push($errors, "Password is required");
    }

    if (count($errors) == 0) {
        $password = md5($password);
        $query = "SELECT * FROM users WHERE username='$username' and password='$password'";
        $result = mysqli_query($db, $query);
        if (mysqli_num_rows($result) == 1) {
            $_SESSION['username'] = $username;
            $_SESSION['success'] = "You are now logged in";
            header('location: ../index.php');
        } else {
            array_push($errors, "Wrong username/password combination");
        }
    }
}
// !_______________________________

// CREATE TOPIC__________________________________________________________
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

        $title = mysqli_real_escape_string($db, $_POST['title']);
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
// !_______________________________

// CREATE ENTRY__________________________________________________________

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
// !_______________________________
?>

