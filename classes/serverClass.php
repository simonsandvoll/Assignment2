<?php

class Server {
    public function __construct($db) {
        $this->db = $db;
    }

    public function __register ($username, $password) {
        $username = $this->db->escape_string($username);
        $password = $this->db->escape_string($password);
        $password = md5($password); // encrypt password 
        $this->__insertInto('users', '(username, password)', "('$username', '$password')");
    }
     
    public function __insertInto($table, $data, $values) {
        $insertQuery = "INSERT INTO $table $data VALUES $values";
        $this->db->dbquery($insertQuery);
    }

    public function __delete($table, $id) {
        $deleteQuery = "DELETE FROM $table WHERE id='$id'";
        $this->db->dbquery($deleteQuery);
    }

    public function __getCreatedBy($username) {
        // check users from database
        $createdBy = null;
        $users = $this->db->__getUsers("username='$username'", 1);
        if ($users) {
            $createdBy = $users[0]->id;
        }
        return $createdBy;
    }

    public function __getTopicInfo($topicId) {
        // check topics from database
        $tId = null;
        $topics = $this->db->__getTopics("id='$topicId'", 1);
        if ($topics) {
            $tId = $topics[0]->id;
        }
        return $tId;
    }

    public function __validateUserData($username, $password_1, $password_2) {
        // check if empty
        if (empty($username)) { return array(false, "username is requried"); }
        if (empty($password_1)) { return array(false, "password is required"); }
        if ($password_1 != $password_2) { return array(false, "The two passwords do not match"); } 
        $password = $password_1;
        
        // regex password check
        // check if password is longer than 5 characters
        if (!preg_match("/^.{5,}$/", $password)) {
            return array(false, "Password must be longer than 5 letters"); 
        } 
        // check if there is at least one lowercase character
        if (!preg_match("/[a-z]/", $password)) {
            return array(false, "Password must have at least one lowercase character!");
        }
        // check if there is at least one uppercase character 
        if (!preg_match("/[A-Z]/", $password)) { 
            return array(false, "Password must have at least one uppercase character!");
        }
        // regex username check
        // check if username is between 3-8 characters
        if (!preg_match("/^.{3,8}$/", $username)) {
            return array(false, "Username must be between 3-8 characters!");
        }
        // check if username only includes letters, number and '_'
        if (preg_match("/[^a-zA-Z0-9_-]/", $username)) {
            return array(false, "Username must only include letters, numbers and underscores!");
        }
        return array(true, "success!");
    }

    
    public function __validateLoginData($username, $password) {
        // check if empty
        if (empty($username)) { return array(false, "username is requried"); }
        if (empty($password)) { return array(false, "password is required"); }

        return array(true, "success!");
    }

    public function __validateTopicData($title, $description) {
        // check if empty
        if (empty($title)) { return array(false, "title is requried"); }
        if (empty($description)) { return array(false, "description is required"); }

        // check if title is longer than 3 characters
        if (!preg_match("/^.{3,}$/", $title)) {
            return array(false, "Title must be longer than 3 letters"); 
        }
        // check if content is longer than 5 characters
        if (!preg_match("/^.{5,}$/", $description)) {
            return array(false, "description must be longer than 5 letters"); 
        }
        return array(true, "success!");
    }
    
    public function __validateEntryData($title, $content, $topicId) {
        // check if empty
        if (empty($title)) { return array(false, "title is requried"); }
        if (empty($content)) { return array(false, "content is required"); }
        if (empty($topicId)) { return array(false, "topic is required"); }

        // check if title is longer than 3 characters
        if (!preg_match("/^.{3,}$/", $title)) {
            return array(false, "Title must be longer than 3 letters"); 
        }
        // check if content is longer than 5 characters
        if (!preg_match("/^.{5,}$/", $content)) {
            return array(false, "Content must be longer than 5 letters"); 
        }
        return array(true, "success!");
    }
}

?>