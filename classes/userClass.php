<?php
class User {
    function __construct($id, $username, $password, $type) {
        $this->id = $id;      // Primary key
        $this->username = $username;
        $this->password = $password;
        $this->type = $type;
    }

    public function __toString(){
        $tempId = $this->id;
        echo "
            <tr>
                <td>$this->id</td>
                <td>$this->username</td>
                <td>$this->type</td>
                <td><a class='btn btn-danger' href='../src/server.php?deleteUserId=$tempId'>Delete User?</a></td>
            </tr>";
    }
}
?>