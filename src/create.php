<?php include('server.php'); ?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php if (isset($_GET['topic']) && $_GET['topic'] == 1) : ?> Create Topic 
           <?php elseif (isset($_GET['topic']) && $_GET['topic'] == 0) : ?> Write entry 
           <?php endif ?>
    </title>
</head>
<body>

<h2><?php if (isset($_GET['topic']) && $_GET['topic'] == 1) : ?> Create Topic 
           <?php elseif (isset($_GET['topic']) && $_GET['topic'] == 0) : ?> Write entry 
           <?php endif ?></h2>
<div>
    <?php if (isset($_SESSION['username'])) : ?>
        <?php if (isset($_GET['topic']) && $_GET['topic'] == 1) : ?> 
            <?php include('errors.php'); ?>
            <form action='create.php' method="post">
                <label for="title">Topic title: </label>
                <input type="text" name="title">
                <label for="description">Topic description</label>
                <textarea name="description" cols="30" rows="10"></textarea>
                <input type="submit" value="Submit" name="createTopic">
            </form>
        <?php elseif (isset($_GET['topic']) && $_GET['topic'] == 0) : ?>
            <?php include('errors.php'); ?>
            <form action='create.php' method="post">
                <label for="title">Entry title: </label>
                <input type="text" name="title">
                <label for="content">Entry content</label>
                <textarea name="content" cols="30" rows="10"></textarea>
                <label for="topic">Select entry topic</label>
                <?php include_once('dropdown.php') ?>
                <input type="submit" value="Submit" name="writeEntry">
            </form>
        <?php endif ?>
    <?php elseif (!isset($_SESSION['username'])) : ?>
        <b> must log in before creating </b><br>
        <a href="./login.php">Log in</a>
        <a href="./register.php">Register now</a>
    <?php endif ?>
    <a href="../index.php">back</a>
</div>
</body>
</html>