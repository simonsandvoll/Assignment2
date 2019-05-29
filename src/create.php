<?php
include('server.php'); 

?>
<!-- CREATE TOPIC OR ENTRY -->
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap-theme.min.css" crossorigin="anonymous">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" crossorigin="anonymous"></script>
    <title><?php if (isset($_GET['topic']) && $_GET['topic'] == 1) : ?> Create Topic 
           <?php elseif (isset($_GET['topic']) && $_GET['topic'] == 0) : ?> Write entry 
           <?php endif ?>
    </title>
</head>
<body>

<div class="container-fluid">
    <h2><?php if (isset($_GET['topic']) && $_GET['topic'] == 1) : ?> Create Topic 
        <?php elseif (isset($_GET['topic']) && $_GET['topic'] == 0) : ?> Write entry 
        <?php endif ?>
    </h2>
    <?php if (isset($_SESSION['username'])) : ?>
        <?php if (isset($_GET['topic']) && $_GET['topic'] == 1) : ?> 
            <?php include('errors.php'); ?>
            <form action='create.php' method="post">
                <div class="form-group">
                    <label for="title">Topic title: </label>
                    <input class="form-control" type="text" name="title">
                </div>
                <div class="form-group">
                    <label for="description">Topic description</label>
                    <textarea name="description" class="form-control"></textarea>
                    
                </div>
                <div class="form-group float-right">
                    <input class="btn btn-primary" type="submit" value="Submit" name="createTopic">
                </div>
            </form>
        <?php elseif (isset($_GET['topic']) && $_GET['topic'] == 0) : ?>
            <?php include('errors.php'); ?>
            <form action='create.php' method="post">
                <div class="form-group">
                    <label for="title">Entry title: </label>
                    <input class="form-control" type="text" name="title">
                </div>
                <div class="form-group">
                    <label for="content">Entry content</label>
                    <textarea name="content" class="form-control"></textarea>
                </div>
                <div class="form-group">
                    <label for="topic">Select entry topic</label>
                    <?php include 'dropdown.php'; ?>
                </div>
                <div class="form-group float-right">
                    <input class="btn btn-primary" type="submit" value="Submit" name="writeEntry">
                </div>
            </form>
        <?php endif ?>
    <?php elseif (!isset($_SESSION['username'])) : ?>
        <b> You must log in before creating</b><br>
        <a class="btn btn-primary" href="./login.php">Log in</a>
        <a class="btn btn-primary" href="./register.php">Register now</a>
    <?php endif ?>
    <a class="btn btn-danger" href="../index.php">&lt;back</a>
</div>
</body>
</html>

<?php  include 'errors.php'; ?>
