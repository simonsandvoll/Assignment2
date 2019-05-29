<!-- REGISTER A NEW USER -->
<?php include('server.php'); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap-theme.min.css" crossorigin="anonymous">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" crossorigin="anonymous"></script>
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Register now</title>
</head>
<body>
    <div class="container-fluid">
        <h2>Register</h2>
        <form method="post" action="register.php">
            <?php include('errors.php'); ?>
            <div class="form-group">
                <label for="username">Username</label>
                <input class="form-control" type="text" name="username">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input class="form-control" type="password" name="password_1">
            </div>
            <div class="form-group">
                <label for="password">Confirm password</label>
                <input class="form-control" type="password" name="password_2">
            </div>
            <div class="form-group">
                <button class="btn btn-primary" type="submit" name="regUser">Register</button>
            </div>
            <p>
                Already a member? <a class="alert-link" href="login.php">Sign in</a>
            </p>
            <a class="btn btn-danger" href="../index.php">&lt;back</a>
        </form>
    </div>
</body>
</html>