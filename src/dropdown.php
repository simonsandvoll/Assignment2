<?php

$conn = new mysqli('localhost', 'root', '', 'urbandictionary') 
or die ('Cannot connect to db');

$result = $conn->query("SELECT id, title FROM topics");

$numRows = mysqli_num_rows($result);
if ($numRows == 0) {
    echo '<b style="color: red;"> missing topics, create topic before entry </b>';
    echo '<a href="./create.php?topic=1">Create Topic</a><br>';
} else {
    echo "<select name='topic' id='topicDropdown'>";
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