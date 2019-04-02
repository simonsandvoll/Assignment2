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
require '../classes/db.php';

if (isset($_GET['user'])) {
    $username = $_GET['user'];
    showUsers($username);
}

function showUsers($username) {
    $errors = array();
    // database connection
    $db = db::getInstance();

    $users = $db->__getUsers("username<>'$username'");
    if ($users == null) {
        array_push($errors, "no users found");
    } else {
        echo "
            <h1>Users</h1>
            <table class='table'>
                <thead class='thead-dark'>
                    <th scope='col'>Id</th>
                    <th scope='col'>Username</th>
                    <th scope='col'>Type</th>
                    <th scope='col'>Delete?</th>
                </thead>
                <tbody>
            ";
        foreach($users as $user) {
            $user->__toString();
        }
        echo "
            </tbody></table>
            <a class='btn btn-danger' href='../index.php'>&lt;Back</a></div>
        ";
    }
    include('../src/errors.php');
    $db->close();
}

?>