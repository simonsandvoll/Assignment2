<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap-theme.min.css" crossorigin="anonymous">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" crossorigin="anonymous"></script>
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Search...</title>
</head>
<body>
    <div class="container-fluid mt-3">
        <form action="search.php" method="get" class="form-inline">
            <div class="form-group mb-2">
                <input class="form-control" type="search" name="search" placeholder="search...">
            </div>
            <div class="form-group mb-2">
                <input class="btn btn-primary" type="submit" value="search">
            </div>
        </form>
        <a class="btn btn-danger" href="../index.php">&lt;back</a>
</body>
</html>
<?php

$errors = array();
$topicErrors = array();
$entryErrors = array();

$minStringLength = 3;


include '../classes/db.php';

// database connection
$db = db::getInstance();


if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
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

if (isset($_GET['search'])) {
    unset($search, $tQuery, $tResult, $tNumRows);
    $search = $_GET['search'];

    if (strlen($search) >= $minStringLength) {
        $search = htmlspecialchars($search);
        $search = $db->escape_string($search);

        $topics = $db->__searchDb("topics", "(t.title, t.description)", $search);

       if ($topics == null) {
           array_push($topicErrors, "no topics found with: '$search'");
       } else {
            echo '<div class="mt-3 p-3 border bg-light"><h5>In <b>Topics</b> your search found this: </h5>';  
            foreach ($topics as &$topic) {
                $topic->__toString();
                if ($username != '') {
                    echo "<a class='btn btn-primary' href='./showEntries.php?id=$topic->id&user=$username'>Show All Entries($topic->entryCount)</a>";
                } else {
                    echo "<a class='btn btn-primary' href='./showEntries.php?id=$topic->id'>Show All Entries($topic->entryCount)</a>";
                }
           }
           echo '</div>';
       }

       $entries = $db->__searchDb("entries", "(title, content)", $search);

       if ($entries == null) {
            array_push($entryErrors, "no entries found with: '$search'");
       } else {
            echo '<div class="mt-3 p-3 border bg-light "><p>In <b>Entries</b> your search found this: </p>';  
            foreach ($entries as &$entry) {
                $entry->__toString();
                $eId = $entry->id;
                $tId = $entry->topicId;
                $entryTopics = $db->__getTopics("id=$tId", 1);
                $topicTitle = $entryTopics[0]->title;
                if ($username == $entry->createdBy || $userType == 'Admin')  {
                    echo "<a  class='btn btn-danger' href='server.php?deleteId=$eId'>Delete</a>";
                }
                echo "
                    <p>This entry is under the $topicTitle topic<p>
                    <a class='btn btn-primary' href='./showEntries.php?id=$tId'>show all entries under the $topicTitle topic</a>";
            }
            echo '</div></div>';
       }
    } else {
        array_push($topicErrors, "search too short");
    }
    if (sizeof($topicErrors) > 0 || sizeof($entryErrors) > 0 && sizeof($errors) == 0) {
        $errors = array_merge($entryErrors, $topicErrors);
    }
    include('./errors.php');
} 

?>