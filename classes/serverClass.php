<?php

class Server {
    
    /**
     * The server constructor defines the connection to the database. 
     * @param { database connection } $db -> a connection to the database
    */
    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * When the user signs up and creates a user insert that user into the user table in the database
     * Runs the __insertInto function from db.php
     * @param { string } $username -> the username from the registration form
     * @param { string } $password -> the password from the registration form
    */
    public function __register ($username, $password) {
        $username = $this->db->escape_string($username);
        $password = $this->db->escape_string($password);
        $password = md5($password); // encrypt password 
        $this->__insertInto('users', '(username, password)', "('$username', '$password')");
    }
     
    /**
     * Function that inserts information into the database
     * Runs the dbQuery function from db.php 
     * @param { string } $table -> defines the table the data is going to be inserted into
     * @param { string } $data -> the columns the values are going into
     * @param { string } $password -> the values inserted into the table
    */
    public function __insertInto($table, $data, $values) {
        $insertQuery = "INSERT INTO $table $data VALUES $values";
        $this->db->dbquery($insertQuery);
    }
 
    /**
     * Function that deletes information from the database
     * Runs the dbQuery function from db.php
     * @param { string } $table -> defines the table the data is going to be inserted into
     * @param { int } $id -> the id of the row to be deleted.
    */
    public function __delete($table, $id) {
        $deleteQuery = "DELETE FROM $table WHERE id='$id'";
        $this->db->dbquery($deleteQuery);
    }

    /**
     * Get id of the user that has created some content on the site.
     * Runs the __getUsers function from db.php
     * @param { string } $username -> the username to search for.
     * @return { int } $createdBy -> the id of the user
    */
    public function __getCreatedBy($username) {
        $createdBy = null;
        $users = $this->db->__getUsers("username='$username'", 1);
        if ($users) {
            $createdBy = $users[0]->id;
        }
        return $createdBy;
    }

    /**
     * Get id of a topic
     * Runs the __getTopics function from db.php
     * @param { string } $username -> the username to search for.
     * @return { int } $tId -> the id of the topic 
    */
    public function __getTopicInfo($topicId) {
        $tId = null;
        $topics = $this->db->__getTopics("id='$topicId'", 1);
        if ($topics) {
            $tId = $topics[0]->id;
        }
        return $tId;
    }

    /**
     * Validate the form data before inserting it into the database
     * Check if any inputfield is empty, valid, etc. 
     * @param { string } $username -> the username of the new user.
     * @param { string } $password_1 -> the first inputfield for the password
     * @param { string } $password_2 -> the second inputfield for the password, both passwords have to match
     * @return { array } with [bool, string] -> if bool is true the user info is valid, if not display error message 
    */
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

    /**
     * Check if any inputfield is empty when the user logs in.
     * @param { string } $username -> the username of the user.
     * @param { string } $password -> the password of the user.
     * @return { array } with [bool, string] -> if bool is true the user info is valid, if not display error message 
    */
    public function __validateLoginData($username, $password) {
        // check if empty
        if (empty($username)) { return array(false, "username is requried"); }
        if (empty($password)) { return array(false, "password is required"); }

        return array(true, "success!");
    }

    /**
     * When creating a topic validate the info from the form
     * Check if any inputfield is empty and valid.
     * @param { string } $title -> the title of the new topic.
     * @param { string } $description -> the description of the new topic.
     * @return { array } with [bool, string] -> if bool is true the topic info is valid, if not display error message 
    */
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

    /**
     * When creating an entry validate the info from the form
     * Check if any inputfield is empty and valid.
     * @param { string } $title -> the title of the new entry.
     * @param { string } $content -> the content of the new entry.
     * @param { string } $topicId -> the topic id of the new entry, meaning what topic the entry is under.
     * @return { array } with [bool, string] -> if bool is true the entry info is valid, if not display error message 
    */
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