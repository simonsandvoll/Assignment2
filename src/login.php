<?php include('server.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login</title>
</head>
<body>
    <div>
        <h2>Login</h2>
        <form method="post" action="login.php">
            <?php include('errors.php'); ?>
            <div>
                <label>Username</label>
                <input type="text" name="username" >
            </div>
            <div>
                <label>Password</label>
                <input type="password" name="password">
            </div>
            <div>
                <button type="submit" name="loginUser">Login</button>
            </div>
            <p>
                Not yet a member? <a href="register.php">Sign up</a>
            </p>
            <a href="../index.php">back</a>
        </form>
    </div>
</body>
</html>
