<?php 

// if no database exists create one
try {
    $mysqli = new mysqli("localhost", "root", "");
} catch (\Exception $e) {
    echo $e->getMessage(), PHP_EOL;
}
if ($mysqli->select_db('urbandictionary') === false) {
    // Create db
    $dbQuery = 'CREATE DATABASE urbandictionary';
    $mysqli->query($dbQuery);
    $mysqli->select_db('urbandictionary');
    createTables($mysqli);
}

function createTables($mysqli) {
    $userQuery = "CREATE TABLE users (
       id INT NOT NULL AUTO_INCREMENT,
       username VARCHAR(255) NOT NULL,
       password VARCHAR(255) NOT NULL,
       type VARCHAR(255) NOT NULL DEFAULT 'Author',
       CONSTRAINT pk_user PRIMARY KEY (id)
    )";
    try {
        $mysqli->query($userQuery);
    } catch (\Exception $e) {
        echo $e->getMessage(), PHP_EOL;
    }

    // insert admin user 
    $password = md5('Admin');
    $adminQuery = "INSERT INTO users (username, password, type) VALUES ('simon', '$password', 'Admin')";
    
    try {
        $mysqli->query($adminQuery);
    } catch (\Exception $e) {
        echo $e->getMessage(), PHP_EOL;
    }
    $topicQuery = "CREATE TABLE topics (
        id INT NOT NULL AUTO_INCREMENT,
        title VARCHAR(255) NOT NULL,
        description VARCHAR(255) NOT NULL,
        createdBy INT NOT NULL,
        CONSTRAINT pk_topics PRIMARY KEY (id),
        CONSTRAINT fk_users_topics FOREIGN KEY (createdBy) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE
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
        CONSTRAINT fk_users_entries FOREIGN KEY (createdBy) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE,
        CONSTRAINT fk_topics_entries FOREIGN KEY (topicId) REFERENCES topics(id) ON UPDATE CASCADE ON DELETE CASCADE
    );";
    try {
        $mysqli->query($entryQuery);
    } catch (\Exception $e) {
        echo $e->getMessage(), PHP_EOL;
    }

    // add indexes
    $topicIndex = "ALTER TABLE topics ADD FULLTEXT KEY ft_topics (title, description);";
    try {
        $mysqli->query($topicIndex);
    } catch (\Exception $e) {
        echo $e->getMessage(), PHP_EOL;
    }
    $entryIndex = "ALTER TABLE entries ADD FULLTEXT KEY ft_entries (title, content);";
    try {
        $mysqli->query($entryIndex);
    } catch (\Exception $e) {
        echo $e->getMessage(), PHP_EOL;
    }
    echo 'tables created';
}

$mysqli->close();



?>