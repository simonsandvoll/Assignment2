<form action="search.php" method="get">
    <label for="search">What are you looking for?</label>
    <input type="search" name="search">
    <input type="submit" value="search">
</form>
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
            echo '<p>In <b>Topics</b> your search found this: </p><div>';  
            foreach ($topics as &$topic) {
                $topic->__toString();
                if ($username != '') {
                    echo "<a href='./showEntries.php?id=$topic->id&user=$username'>Show All Entries($topic->entryCount)</a>";
                } else {
                    echo "<a href='./showEntries.php?id=$topic->id'>Show All Entries($topic->entryCount)</a>";
                }
           }
           echo '</div>';
       }

       $entries = $db->__searchDb("entries", "(title, content)", $search);

       if ($entries == null) {
            array_push($entryErrors, "no entries found with: '$search'");
       } else {
            echo '<p>In <b>Entries</b> your search found this: </p><div>';  
            foreach ($entries as &$entry) {
                $entry->__toString();
                $eId = $entry->id;
                $tId = $entry->topicId;
                $entryTopics = $db->__getTopics("id=$tId", 1);
                $topicTitle = $entryTopics[0]->title;
                if ($username == $entry->createdBy || $userType == 'Admin')  {
                    echo "<a href='server.php?deleteId=$eId'>Delete</a>";
                }
                echo "
                    <p>This entry is under the $topicTitle topic<p>
                    <a href='./showEntries.php?id=$tId'>show all entries under the $topicTitle topic</a>";
            }
            echo '</div>';
       }
       echo '<br><a href="../index.php">back</a>';
    } else {
        array_push($topicErrors, "search too short");
    }
    if (sizeof($topicErrors) > 0 || sizeof($entryErrors) > 0 && sizeof($errors) == 0) {
        $errors = array_merge($entryErrors, $topicErrors);
    }
    include('./errors.php');
} 

?>