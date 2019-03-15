<?php

$errors = array();
// database connection
$db = mysqli_connect('localhost', 'root', '', 'urbandictionary');

if (isset($_GET['id'])) {
    unset($topicId);
    $topicId = $_GET['id'];
    $topicQuery = "SELECT title FROM topics WHERE id = '$topicId' LIMIT 1";
    $topicResult = mysqli_query($db, $topicQuery);
    $numTRows = mysqli_num_rows($topicResult);
    if ($numTRows == 0) {
        echo '<p>No topics found!</p>';
    } else {
        while($tRow = $topicResult->fetch_assoc()) {
            unset ($topicTitle);
            $topicTitle = $tRow['title'];
        }
    }
    $entryQuery = "SELECT * FROM entries WHERE topicId = '$topicId'";
    $entryResult = mysqli_query($db, $entryQuery);

    $numRows = mysqli_num_rows($entryResult);
    if ($numRows == 0) {
        echo '<p>No entries found!</p>';
    } else {

        echo "
            <h1>$topicTitle</h1>
            <div class='entries'>
            ";
        while ($row = $entryResult->fetch_assoc()) {
            unset($title, $content, $createdBy, $topic);
            
            $userId = $row["createdBy"];
            $userQuery = "SELECT username FROM users WHERE id='$userId' LIMIT 1";
            $userResult = mysqli_query($db, $userQuery);
            $users = mysqli_fetch_assoc($userResult);

            if (mysqli_num_rows($userResult) == 1) {
                $createdBy = $users['username'];
            } else {
                array_push($errors, 'did not find user');
            }

            $title = $row['title']; 
            $content = $row['content']; 
            if (count($errors) == 0) {
            echo "
                <h3>$title</h3>
                <p>$content<p>
                <span>entry written by: $createdBy</span>
                <p>under the $topicTitle topic</p>";
            }
        }
        echo "
            </table>
            <a href='./create.php?topic=1'>Create Topic</a>
            <a href='./create.php?topic=0'>Write entry</a>
            <a href='../index.php'>Back</a>
            ";
    }
}
?>
