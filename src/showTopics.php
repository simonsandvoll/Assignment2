<?php

require './classes/db.php';

$errors = array();
// database connection
$db = db::getInstance();

$popularityQuery = "ORDER BY entryCount DESC";
$chronologicalQuery = "ORDER BY t.id";
$chosenQuery = '';
$userType = '';

if(isset($_SESSION['username'])) {
    $username = $_SESSION['username'];

    // find user type
    $users = $db->__getUsers("username='$username'", 1);
    if ($users) {
        $userType = $users[0]->type;
    }

    if (isset($_COOKIE['sorting'.$username.''])) {
        $sortMethod = $_COOKIE['sorting'.$username.''];
        $sortMethod == 1 ? $chosenQuery = $popularityQuery : $chosenQuery = $chronologicalQuery;
    } else {
        $chosenQuery = $chronologicalQuery;
    }
} else if (!isset($_SESSION['username']) && isset($_SESSION['sortingMethod']) ){
    $sortMethod = $_SESSION['sortingMethod'];
    $sortMethod == 1 ? $chosenQuery = $popularityQuery : $chosenQuery = $chronologicalQuery;
    $username = '';
} else {
    $chosenQuery = $chronologicalQuery;
    $username = '';
}

$topics = $db->__getTopics(null, null, $chosenQuery);

if ($topics == null) {
    array_push($errors, "no topics found");
} else {
    /* echo '<pre>'; print_r($topics); echo '</pre>'; */
    echo "
        <div>
            <h2>Topics</h2>
        ";
    foreach($topics as $topic) {
        $topic->__toString();
        if ($username != '') {
            echo "<a href='./src/showEntries.php?id=$topic->id&user=$username'>Show All Entries($topic->entryCount)</a>";
        } else {
            echo "<a href='./src/showEntries.php?id=$topic->id'>Show All Entries($topic->entryCount)</a>";
        }
        $entries = $db->__getEntries("topicId=$topic->id", 1);
        if ($entries == null) {
            array_push($errors, "no entries found");
            if ($username == $topic->createdBy || $userType == 'Admin') {
                $tId = $topic->id;
                $cb = $topic->createdBy;
                echo "<a href='./src/server.php?deleteTopicId=$tId&user=$cb'>Delete topic</a>";
            }
        } else {
            foreach($entries as $entry) {
                $entry->__toString();
                if ($username == $entry->createdBy || $userType == 'Admin') {
                    $eId = $entry->id;
                    echo "<a href='./src/server.php?deleteId=$eId'>Delete</a>";
                }
            }
        }
    }
}
$db->close();

?>
