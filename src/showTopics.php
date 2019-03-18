<?php

if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
} else {
    $username = '';
}
$errors = array();
// database connection
$db = mysqli_connect('localhost', 'root', '', 'urbandictionary');
$popularityQuery = "SELECT t.* FROM topics t INNER JOIN entries e ON t.id = e.topicId GROUP BY t.id ORDER BY COUNT(e.topicId) DESC";
$chronologicalQuery = "SELECT * FROM topics";
$topicQuery = '';

if(isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    if (isset($_COOKIE['sorting'.$username.''])) {
        $sortMethod = $_COOKIE['sorting'.$username.''];
        $sortMethod == 1 ? $topicQuery = $popularityQuery : $topicQuery = $chronologicalQuery;
    } else {
        $topicQuery = $chronologicalQuery;
    }
} else {
    $topicQuery = $chronologicalQuery;
}

$topicResult = mysqli_query($db, $topicQuery);

$numberRows = mysqli_num_rows($topicResult);
if ($numberRows == 0) {
    array_push($errors, 'no entries found');
} else {
    echo "
        <div>
        <h2>Topics</h2>";
    while ($row = $topicResult->fetch_assoc()) {
        unset($tId, $title, $description, $createdBy);
        
        $userId = $row["createdBy"];
        $userQuery = "SELECT username FROM users WHERE id='$userId' LIMIT 1";
        $userResult = mysqli_query($db, $userQuery);
        $users = mysqli_fetch_assoc($userResult);

        if (mysqli_num_rows($userResult) == 1) {
            $createdBy = $users['username'];
        } else {
            array_push($errors, 'did not find user');
        }
        $tId = $row['id'];
        $title = $row['title']; 
        $description = $row['description']; 
        if (count($errors) == 0) {
        echo "
            <h3>$title</h3>
            <p>$description</p>
            <span>created By: $createdBy</span><br>
            <a href='./src/showEntries.php?id=$tId&user=$username'>Show All Entries</a>
            <h4>Entries: </h4>";
        }
        $tId = $row['id'];
        $entryQuery = "SELECT * FROM entries WHERE topicId='$tId' LIMIT 1";
        $entryResult = mysqli_query($db, $entryQuery);

        $numRows = mysqli_num_rows($entryResult);
        if ($numRows == 0) {
        array_push($errors, 'no entries found');
        echo "<a href='./src/server.php?deleteTopicId=$tId'>Delete topic</a>";
        } else {
            echo "<div class='entry'>";
            while ($eRow = $entryResult->fetch_assoc()) {
                unset($eId, $eTitle, $content, $eCreatedBy);
                
                $userId = $eRow["createdBy"];
                $userQuery = "SELECT username FROM users WHERE id='$userId' LIMIT 1";
                $userResult = mysqli_query($db, $userQuery);
                $users = mysqli_fetch_assoc($userResult);
                $eId = $eRow['id'];
                if (mysqli_num_rows($userResult) == 1) {
                    $eCreatedBy = $users['username'];
                } else {
                    array_push($errors, 'did not find user');
                }

                $eTitle = $eRow['title']; 
                $content = $eRow['content']; 
                if (count($errors) == 0) {
                    echo "
                        <b>$eTitle</b>
                        <p>$content</p>
                        <span>entry created by: $eCreatedBy</span>
                        ";
                    if ($username == $eCreatedBy) {
                        echo "<a href='./src/server.php?deleteId=$eId'>Delete</a>";
                    }
                }
            }
            echo '</div>';
        }
    }
    echo "</div>";
}

?>
