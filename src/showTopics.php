<?php

require './classes/db.php';
require './classes/sessionClass.php';
// database connection
$db = db::getInstance();
$session = new Session($db);
$errors = array();


$popularityQuery = "ORDER BY entryCount DESC";
$chronologicalQuery = "ORDER BY t.id";
$chosenQuery = '';
$userType = '';

$username = $session->__getSession('username');
$sortingMethod = $session->__getSession('sortingMethod');

if($username != null) {
    $username = $session->__getSession('username');

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
} else if ($username == null && $sortingMethod != null ){
    $sortMethod = $session->__getSession('sortingMethod');
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
    echo "
        <div>
            <h2>Topics</h2>
        ";
    foreach($topics as $topic) {
        echo "<div  class='mt-3 p-3 border bg-light'>";
        $topic->__toString();
        if ($username != '') {
            echo "<a class='btn btn-primary' href='./src/showEntries.php?id=$topic->id&user=$username'>Show All Entries($topic->entryCount)</a>";
        } else {
            echo "<a class='btn btn-primary' href='./src/showEntries.php?id=$topic->id'>Show All Entries($topic->entryCount)</a>";
        }
        $entries = $db->__getEntries("topicId=$topic->id", 1);
        if ($entries == null) {
            array_push($errors, "no entries found");
            if ($username == $topic->createdBy || $userType == 'Admin') {
                $tId = $topic->id;
                $cb = $topic->createdBy;
                echo "<a class='btn btn-danger' href='./src/server.php?deleteTopicId=$tId&user=$cb'>Delete topic</a>";
            }
        } else {
            foreach($entries as $entry) {
                $entry->__toString();
                if ($username == $entry->createdBy || $userType == 'Admin') {
                    $eId = $entry->id;
                    echo "<a class='btn btn-danger' href='./src/server.php?deleteId=$eId'>Delete</a>";
                }
            }
        }
        echo '</div>';
    }
    echo '</div>';
}
$db->close();

?>
