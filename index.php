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
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>home</title>
</head>
<body>

<h2>Home Page</h2>
<div>
    <?php if (!isset($_SESSION['username'])) : ?>
        <div>
            <h3>
            <?php 
                echo 'Welcome guest!';
            ?>
            </h3>
            <a href="./src/login.php">Login</a>
            <a href="./src/register.php">Register now</a>
        </div>
    <?php endif ?>
  	<?php if (isset($_SESSION['success'])) : ?>
      <div>
      	<h3>
          <?php 
          	echo $_SESSION['success']; 
          	unset($_SESSION['success']);
          ?>
      	</h3>
      </div>
  	<?php endif ?>
    <?php  if (isset($_SESSION['username'])) : ?>
    	<p>Welcome <strong><?php echo $_SESSION['username']; ?></strong></p>
        <?php  if (isset($_COOKIE['sorting'.$_SESSION['username']])) : ?>
            <p>You are sorting with <?php echo $_COOKIE['sorting'.$_SESSION['username']]; ?></p>
        <?php endif ?>
    	<p> <a href="index.php?logout='1'" style="color: red;">logout</a> </p>
        <a href="./src/create.php?topic=1">Create Topic</a>
        <a href="./src/create.php?topic=0">Write entry</a>
    <?php endif ?>
    <form action="./src/search.php" method="get">
        <label for="search">What are you looking for?</label>
        <input type="search" name="search">
        <input type="submit" value="search">
    </form>
    <form action="./src/server.php" method="get">
        <label for="sortingMethod">Sort Topics</label>
        <select name="sortingMethod" id="sort">
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
        <input type="submit" value="sort">
    </form>
    <?php include('./src/showTopics.php'); ?>
    <?php include('./src/errors.php'); ?>
</div>
</body>
</html>