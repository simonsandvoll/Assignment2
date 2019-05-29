<?php

include_once('config/setup.php');
include('config/config.php');
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

    /**
     * Return an instance of the object if the object does not already exist.
     * @return { instance } database instance if none exist
    */
    public static function getInstance() {
        if (!self::$instance instanceof self) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    /**
     * The clone and wakeup methods prevents external instantiation of copies of the class,
     * thus eliminating the possibility of duplicate objects.
    */
    public function __clone() {
        trigger_error('Clone is not allowed.', E_USER_ERROR);
    }
    public function __wakeup() {
        trigger_error('Deserializing is not allowed.', E_USER_ERROR);
    }

    /**
     * Runs the construct function in the partent class (database) with the database information found in the config file
    */
    private function __construct() {
        parent::__construct($this->dbHost, $this->user, $this->pass, $this->dbName);
        
        if (mysqli_connect_error()) {
            exit('Connect Error (' . mysqli_connect_errno() . ') ' . mysqli_connect_error());
        }
        parent::set_charset('utf-8');
    }
  
    /**
     * Queries the database with a query that does not return anything (update, insert into, etc.)
     * @return { bool } if the query was successful return true (if not return nothing)
    */
    public function dbquery($query) {
        if($this->query($query)) {
            return true;
        }
    }

    /**
     * Escapes special characters in a string for use in an SQL statement, taking into account the current charset of the connection
     * @return { string } returns the query ready string
    */
    public function escape_string ($string) {
        return $this->real_escape_string($string);
    }

    /**
     * Gets users from the database, this query can be with a condition and a limit, none, or only one of them.
     * Creates an array of User objects if query is successful
     * @param { string } $condition -> a string that is inserted into the WHERE clause of the statement if any
     * @param { string } $limit -> a string that is inserted into the LIMIT clause of the statement if any
     * @return { array } returns array of users if query successful (returns null if not)
    */
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

    /**
     * Gets topics from the database, this query can be with; a condition, a limit and $order, none or any combination of them.
     * Creates an array of Topic objects if query is successful
     * @param { string } $condition -> a string that is inserted into the WHERE clause of the statement if any
     * @param { string } $limit -> a string that is inserted into the LIMIT clause of the statement if any
     * @param { string } $order -> a string that can be inserted into SQL statment. Can be for example like this; ORDER BY t.id.
     * @return { array } returns array of topics if query successful (returns null if not)
    */
    public function __getTopics($condition = null, $limit = null, $order = null) {
        $topics = array();
        if ($condition != NULL && $limit == NULL) {
            $query = "SELECT t.*, count(e.topicId) as entryCount FROM topics t LEFT OUTER JOIN entries e ON t.id = e.topicId WHERE t.$condition GROUP BY t.id";
        } else if ($condition != null && $limit != null) {
            $query = "SELECT t.*, count(e.topicId) as entryCount FROM topics t LEFT OUTER JOIN entries e ON t.id = e.topicId WHERE t.$condition GROUP BY t.id LIMIT $limit";
        } else if ($order != null && $condition == null && $limit == null) {
            $query = "SELECT t.*, count(e.topicId) as entryCount FROM topics t LEFT OUTER JOIN entries e ON t.id = e.topicId GROUP BY t.id $order";
        } else {
            $query = "SELECT t.*, count(e.topicId) as entryCount FROM topics t LEFT OUTER JOIN entries e ON t.id = e.topicId GROUP BY t.id";
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

    /**
     * Gets entries from the database, this query can be with a condition and a limit, none, or only one of them.
     * Creates an array of Entry objects if query is successful
     * @param { string } $condition -> a string that is inserted into the WHERE clause of the statement if any
     * @param { string } $limit -> a string that is inserted into the LIMIT clause of the statement if any
     * @return { array } returns array of entries if query successful (returns null if not)
    */
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

    /**
     * Gets entries from the database, this query can be with a condition and a limit, none, or only one of them.
     * Creates an array of Entry objects if query is successful
     * @param { string } $table -> a string depicting the table that is going to be searched through
     * @param { string } $match -> a string tells the database what columns to search through
     * @param { string } $search -> a string of what the user inserted into the search field.
     * @return { array } returns array of entries if query successful (returns null if not)
    */
    public function __searchDb($table, $match, $search) {
        $searchResult = array();
        if ($table == 'topics') {
            $query = "SELECT t.*, count(e.topicId) as entryCount FROM $table t LEFT OUTER JOIN entries e ON t.id = e.topicId 
                WHERE MATCH $match AGAINST ('*$search*' IN BOOLEAN MODE)";
        } else  {
            $query = "SELECT * FROM $table WHERE MATCH $match AGAINST ('*$search*' IN BOOLEAN MODE)";
        }
        $result = $this->query($query);
        if ($result) {
            $numRow = $result->num_rows;
            if ($numRow == 0) {
            } else {
                while ($row = $result->fetch_assoc()) {
                    if ($row['id'] != '') {
                        $cbId = $row['createdBy'];
                        $users = $this->__getUsers("id=$cbId", 1);
                        if ($users) { $userCreatedBy = $users[0]->username; } else { $userCreatedBy = $row['createdBy']; }
                        if ($table == 'topics') {
                            $newTopic = new Topic ($row['id'], $row['title'], $row['description'], $userCreatedBy, $row['entryCount']);
                            array_push($searchResult, $newTopic);
                        } else {
                            $newEntry = new Entry ($row['id'], $row['title'], $row['content'], $userCreatedBy, $row['topicId']);
                            array_push($searchResult, $newEntry);
                        }
                    }
                }
                return $searchResult;
            }
        } else {
            return null;
        }
    }

}

?>