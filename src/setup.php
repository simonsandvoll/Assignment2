<?php 

// if no database exists create one
try {
    $mysqli = new mysqli("localhost", "root", "");
} catch (\Exception $e) {
    echo $e->getMessage(), PHP_EOL;
}
if ($mysqli->select_db('urbandictionary') === false) {
    // Create db
    echo 'no database';
    $dbQuery = 'CREATE DATABASE urbandictionary';
    $mysqli->query($dbQuery);
    $mysqli->select_db('urbandictionary');
    createTables($mysqli);
} else {
    echo "database exists";
}

function createTables($mysqli) {
    $userQuery = "CREATE TABLE users (
       id INT NOT NULL AUTO_INCREMENT,
       username VARCHAR(255) NOT NULL,
       password VARCHAR(255) NOT NULL,
       CONSTRAINT pk_user PRIMARY KEY (id)
    )";
    try {
        $mysqli->query($userQuery);
    } catch (\Exception $e) {
        echo $e->getMessage(), PHP_EOL;
    }
    $topicQuery = "CREATE TABLE topics (
        id INT NOT NULL AUTO_INCREMENT,
        title VARCHAR(255) NOT NULL,
        description VARCHAR(255) NOT NULL,
        createdBy INT NOT NULL,
        CONSTRAINT pk_topics PRIMARY KEY (id),
        CONSTRAINT fk_users_topics FOREIGN KEY (createdBy) REFERENCES users(id) ON UPDATE CASCADE ON DELETE RESTRICT
    );";
    try {
        $mysqli->query($topicQuery);
    } catch (\Exception $e) {
        echo $e->getMessage(), PHP_EOL;
    }

    $entryQuery = "CREATE TABLE entries (
        id INT NOT NULL AUTO_INCREMENT,
        title VARCHAR(255) NOT NULL,
        content VARCHAR(255) NOT NULL,
        topicId INT NOT NULL,
        createdBy INT NOT NULL,
        CONSTRAINT pk_entries PRIMARY KEY (id),
        CONSTRAINT fk_users_entries FOREIGN KEY (createdBy) REFERENCES users(id) ON UPDATE CASCADE ON DELETE RESTRICT,
        CONSTRAINT fk_topics_entries FOREIGN KEY (topicId) REFERENCES topics(id) ON UPDATE CASCADE ON DELETE RESTRICT
    );";
    try {
        $mysqli->query($entryQuery);
    } catch (\Exception $e) {
        echo $e->getMessage(), PHP_EOL;
    }

    echo 'tables created';
}

// regex password check
/*
    if (!preg_match("/^.{5,}$/", password)) // check if password is longer than 5 characters
    if (!/[a-z]/.test(password)) // check if there is at least one lowercase character
    if (!/[A-Z]/.test(password)) // check if there is at least one uppercase character
*/

// regex username check
/*
    if (!/^{3,8}$/.test(username)) // check if username is between 3-8 characters
    if (!/[^\w]/.test(username)) // check if username only includes letters, number and '_'
*/

?>