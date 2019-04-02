<?php 
session_start();

if (isset($_GET['logout'])) {
    session_destroy();
    unset($_SESSION['username']);
    header('location: index.php');
}
?> 

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap-theme.min.css" crossorigin="anonymous">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" crossorigin="anonymous"></script>
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>home</title>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand">Urbandictionary</a>
    <ul class="navbar-nav mr-auto">
        <?php if (!isset($_SESSION['username'])) : ?>
        <li class="nav-item"><a class="btn btn-primary" href="./src/login.php">Login</a></li>
        <li class="nav-item"><a class="btn btn-primary" href="./src/register.php">Register now</a></li>
        <?php elseif (isset($_SESSION['username'])) : ?>
        <li class="nav-item"><a class="btn btn-primary" href="./src/create.php?topic=1">Create Topic</a></li>
        <li class="nav-item"><a class="btn btn-primary" href="./src/create.php?topic=0">Write entry</a></li>
        <?php endif ?>
    </ul>
    <form action="./src/search.php" method="get" class="form-inline mr-sm-2">
        <input type="search" name="search" class="form-control mr-sm-2" placeholder="Search">
        <input type="submit" value="search" class="btn btn-outline-success my-2 my-sm-0">
    </form>
    <?php if (isset($_SESSION['usertype'])) :?>
        <div class="nav-item"><span class="badge badge-secondary rounded-pill"><?php echo $_SESSION['usertype']; ?></span></div>
    <?php endif ?>
    <?php if (isset($_SESSION['username'])) : ?>
        <div class="nav-item"><a class="btn btn-danger" href="index.php?logout='1'">logout</a></div>
    <?php endif ?>
</nav>

<div class="container-fluid">
    <h1>Home Page</h1>
    <?php if (!isset($_SESSION['username'])) : ?>
        <div>
            <p>Welcome <strong>guest!</strong></p>
        </div>
    <?php endif ?>
  	<?php if (isset($_SESSION['success'])) : ?>
      <div class="alert alert-success">
      	<h3>
          <?php 
          	echo $_SESSION['success']; 
          	unset($_SESSION['success']);
          ?>
      	</h3>
      </div>
  	<?php endif ?>
    <?php if (isset($_SESSION['username'])) : ?>
    	<p>Welcome <strong><?php echo $_SESSION['username']; ?></strong></p>
        <?php if (isset($_SESSION['usertype'])) :?>
            <?php if ($_SESSION['usertype'] == 'Admin') : ?>
                <a class="btn btn-primary" href=<?php $user = $_SESSION['username']; echo "./src/showUsers.php?user=$user";?>>Show users</a>
            <?php endif ?>
        <?php endif ?>
    <?php endif ?>
    <form action="./src/server.php" method="get" class="form-inline mr-sm-2">
            <div class="form-group mb-2">
                <label for="sortingMethod">Sort Topics</label>
                <select name="sortingMethod" id="sort" class="form-control">
                <?php  if (isset($_SESSION['username'])) : ?>
                    <?php  if (isset($_COOKIE['sorting'.$_SESSION['username']])) : ?>
                        <?php if ($_COOKIE['sorting'.$_SESSION['username']] == 0) : ?>
                            <option value="0" selected>Chronological</option>
                            <option value="1">Popluarity</option>
                        <?php elseif ($_COOKIE['sorting'.$_SESSION['username']] == 1) : ?>
                            <option value="0">Chronological</option>
                            <option value="1" selected>Popluarity</option>
                        <?php endif ?>
                    <?php elseif (!isset($_COOKIE['sorting'.$_SESSION['username']])) : ?>
                        <option value="0">Chronological</option>
                        <option value="1">Popluarity</option>
                    <?php endif ?>
                    <?php elseif (!isset($_SESSION['username'])) : ?>
                    <option value="0">Chronological</option>
                    <option value="1">Popluarity</option>
                <?php endif ?>
                </select>
            </div>
            <input type="submit" value="sort" class="btn btn-primary mb-2">
        </form>
    <?php include('./src/showTopics.php'); ?>
    <?php include('./src/errors.php'); ?>
</div>
</body>
</html>