<?php include('server.php'); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Register now</title>
</head>
<body>
    <h2>Register </h2>
    <form method="post" action="register.php">
        <?php include('errors.php'); ?>
        <div>
        <label>Username</label>
        <input type="text" name="username" value="<?php echo $username; ?>">
        </div>
        <div>
        <label>Password</label>
        <input type="password" name="password_1">
        </div>
        <div>
        <label>Confirm password</label>
        <input type="password" name="password_2">
        </div>
        <div>
        <button type="submit" name="regUser">Register</button>
        </div>
        <p>
            Already a member? <a href="login.php">Sign in</a>
        </p>
        <a href="../index.php">back</a>
    </form>
</body>
</html>