<?php 

session_start();

$username = "";
$errors = array();

// database connection
$db = mysqli_connect('localhost', 'root', '', 'urbandictionary');

// CREATE TOPIC

if (isset($_POST['createTopic'])) {
    if (isset($_SESSION['username'])) {
        $username = $_SESSION['username'];
        // get user from database
        $query = "SELECT * FROM users WHERE username='$username' LIMIT 1";
        $result = mysqli_query($db, $query);
        $user = mysqli_fetch_assoc($result);
        // assign id as user id
        $id = $user['id'];

        $title = mysqli_real_escape_string($db, $_POST['title']);
        $description = mysqli_real_escape_string($db, $_POST['description']);
        $createdBy = $id;
        
        // validate form
        if (empty($title)) { array_push($errors, "title is requried"); }

        if (count($errors) == 0) {
            $query = "INSERT INTO topics (title, description, createdBy)
                    VALUES('$title', '$description', '$createdBy')";
            mysqli_query($db, $query);
            $_SESSION['topicTitle'] = $title;
            $_SESSION['topicSuccess'] = "Created topic";
            header('location: ../index.php');
        }
    } else {
        array_push($errors, 'must be logged in to create topics');
    }
}
?>