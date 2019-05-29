<!-- LOGIN FORM -->
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
    <title>Login</title>
</head>
<body>
    <div class="container-fluid">
        <h2>Login</h2>
        <form method="post" action="login.php">
            <?php include('errors.php'); ?>
            <div class="form-group">
                <label for="username">Username</label>
                <input class="form-control" type="text" name="username" >
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input class="form-control" type="password" name="password">
            </div>
            <div class="form-group">
                <button class="btn btn-primary" type="submit" name="loginUser">Login</button>
            </div>
            <p>
                Not yet a member? <a class="alert-link"  href="register.php">Sign up</a>
            </p>
            <a class="btn btn-danger" class="" href="../index.php">&lt;back</a>
        </form>
    </div>

</body>
</html>
