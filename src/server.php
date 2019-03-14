<?php

session_start();

$username = "";
$errors = array();

// database connection
$db = mysqli_connect('localhost', 'root', '', 'urbandictionary');

// REGISTER USER
if (isset($_POST['reg_user'])) {
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

// LOGIN USER
if (isset($_POST['login_user'])) {
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
?>

