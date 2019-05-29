<?php

/** CREATE DROPDOWN OPTION FOR EACH TOPIC */

$conn = new mysqli('localhost', 'root', '', 'urbandictionary') 
or die ('Cannot connect to db');

$result = $conn->query("SELECT id, title FROM topics");

$numRows = mysqli_num_rows($result);
if ($numRows == 0) {
    echo '<b class="alert alert-warning"> missing topics, create topic before entry </b>';
    echo '<a class="btn btn-primary" href="./create.php?topic=1">Create Topic</a><br>';
} else {
    echo "<select class='form-control' name='topic' id='topicDropdown'>";
    while ($row = $result->fetch_assoc()) {
        unset($id, $title);
        $id = $row['id'];
        $title = $row['title']; 
        echo '<option value="'.$id.'">'.$title.'</option>';
    }
    echo "</select>";
}
$conn->close();
?>