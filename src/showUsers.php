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
            <table>
                <thead>
                    <th>Id</th>
                    <th>Username</th>
                    <th>Type</th>
                    <th>Delete?</th>
                </thead>
                <tbody>
            ";
        foreach($users as $user) {
            $user->__toString();
        }
        echo "
            </tbody></table>
            <a href='../index.php'>Back</a>
        ";
    }
    include('../src/errors.php');
    $db->close();
}

?>