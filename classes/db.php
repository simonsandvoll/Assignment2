<?php

include('config.php');
require_once 'userClass.php';
require_once 'topicClass.php';
require_once 'entryClass.php';

class db extends mysqli {

    // single instance of self shared among all instances
    private static $instance = null;

    // db connection config vars
    private $user = DBUSER;
    private $pass = DBPWD;
    private $dbName = DBNAME;
    private $dbHost = DBHOST;

    //return an instance of the object if the object if it does not already exist.
    public static function getInstance() {
        if (!self::$instance instanceof self) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    // The clone and wakeup methods prevents external instantiation of copies of the class,
    // thus eliminating the possibility of duplicate objects.
    public function __clone() {
        trigger_error('Clone is not allowed.', E_USER_ERROR);
    }
    public function __wakeup() {
        trigger_error('Deserializing is not allowed.', E_USER_ERROR);
    }

    private function __construct() {
        parent::__construct($this->dbHost, $this->user, $this->pass, $this->dbName);
        
        if (mysqli_connect_error()) {
            exit('Connect Error (' . mysqli_connect_errno() . ') ' . mysqli_connect_error());
        }
        parent::set_charset('utf-8');
    }

    public function dbquery($query) {
        if($this->query($query)) {
            return true;
        }
    }
    public function get_result($query) {
        $result = $this->query($query);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row;
        } else {
            return null;
        }
    }

    public function get_rows ($result) {
        return $result->num_rows;
    }

    public function escape_string ($string) {
        return $this->real_escape_string($string);
    }

    public function __getUsers($condition = null, $limit = null) {
        $users = array();
        if ($condition != NULL && $limit != NULL) {
            $query = "SELECT * FROM users WHERE $condition LIMIT $limit";
        } else if ($condition != NULL && $limit == NULL) {
            $query = "SELECT * FROM users WHERE $condition";
        } else if ($condition == NULL && $limit != NULL) {
            $query = "SELECT * FROM users LIMIT $limit";
        } else {
            $query = "SELECT * FROM users";
        }
        $result = $this->query($query);
        if ($result) {
            $numRows = $result->num_rows;
            if ($numRows == 0) {
                return null;
            } else {
                while ($row = $result->fetch_assoc()) {
                    $newUser = new User ($row['id'], $row['username'], $row['password'], $row['type']);
                    array_push($users, $newUser);
                }
                return $users;
            }
        } else {
            return null;
        }
    }

    public function __getTopics($condition = null, $limit = null, $order = null) {
        $topics = array();
        if ($condition != NULL && $limit != NULL) {
            $query = "SELECT t.*, count(e.topicId) as entryCount FROM topics t LEFT OUTER JOIN entries e ON t.id = e.topicId GROUP BY t.id";
        } else if ($condition != NULL && $limit == NULL) {
            $query = "SELECT t.*, count(e.topicId) as entryCount FROM topics t LEFT OUTER JOIN entries e ON t.id = e.topicId WHERE t.$condition GROUP BY t.id";
        } else if ($order != null && $limit != null) {
            $query = "SELECT t.*, count(e.topicId) as entryCount FROM topics t LEFT OUTER JOIN entries e ON t.id = e.topicId WHERE t.$condition GROUP BY t.id LIMIT $limit";
        } else if ($order != null && $condition == null && $limit == null) {
            $query = "SELECT t.*, count(e.topicId) as entryCount FROM topics t LEFT OUTER JOIN entries e ON t.id = e.topicId GROUP BY t.id $order";
        } else {
            $query = "SELECT * FROM topics";
        }
        $result = $this->query($query);
        if ($result) {
            $numRows = $result->num_rows;
            if ($numRows == 0) {
                return null;
            } else {
                while ($row = $result->fetch_assoc()) {
                    $cbId = $row['createdBy'];
                    $users = $this->__getUsers("id=$cbId", 1);
                    if ($users) { $userCreatedBy = $users[0]->username; } else { $userCreatedBy = $row['createdBy']; }
                    $newTopic = new Topic ($row['id'], $row['title'], $row['description'], $userCreatedBy, $row['entryCount']);
                    array_push($topics, $newTopic);
                }
                return $topics;
            }
        } else {
            return null;
        }
    }

    public function __getEntries($condition = null, $limit = null) {
        $entries = array();
        if ($condition != NULL && $limit != NULL) {
            $query = "SELECT * FROM entries WHERE $condition LIMIT $limit";
        } else if ($condition != NULL && $limit == NULL) {
            $query = "SELECT * FROM entries WHERE $condition";
        } else if ($condition == NULL && $limit != NULL) {
            $query = "SELECT * FROM entries LIMIT $limit";
        } else {
            $query = "SELECT * FROM entries";
        }
        $result = $this->query($query);
        if ($result) {
            $numRows = $result->num_rows;
            if ($numRows == 0) {
                return null;
            } else {
                while ($row = $result->fetch_assoc()) {
                    $cbId = $row['createdBy'];
                    $users = $this->__getUsers("id=$cbId", 1);
                    if ($users) { $userCreatedBy = $users[0]->username; } else { $userCreatedBy = $row['createdBy']; }
                    $newEntry = new Entry ($row['id'], $row['title'], $row['content'], $userCreatedBy, $row['topicId']);
                    array_push($entries, $newEntry);
                }
                return $entries;
            }
        } else {
            return null;
        }
    }
}

?>