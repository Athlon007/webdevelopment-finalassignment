<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
        <meta name="theme-color" content="#fffbfa">
        <meta name="robots" content="noindex, nofollow">
        <title>My Opinion - First Time Setup</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
        <link rel="stylesheet" href="/css/login.css">
    </head>
    <body>
        <div class="container">
            <div class="row align-items-center h-100">
                <div class="col-sm-12 col-md-6 mx-auto p-4">
                    <h1>Hi there!</h1>
                    <p>We must setup the first admin account.</p>
                    <form method="POST" id="regiuster-form">
                        <div class="form-group mt-2 mb-2">
                            <label>Username</label>
                            <input type="text" class="form-control" name="username" placeholder="Username" id="username"
                            value="<?php
                            if (isset($username)) {
                                echo $username;
                            } ?>"required />
                        </div>
                        <div class="form-group mt-2 mb-2">
                            <label>E-mail</label>
                            <input type="email" class="form-control" name="email" placeholder="E-mail" id="email"
                            value="<?php if (isset($email)) { echo $email; } ?>" required />
                        </div>
                        <div class="form-group mb-2">
                            <label>Password</label>
                            <input type="password" class="form-control" name="password" placeholder="Password"
                             id="password" required />
                            <small class="form-text text-muted">Password must consist of at least 8 characters,
                                one capital leter, one small letter, one digit and one special character.</small>
                        </div>
                        <div class="form-group mb-2">
                            <label>Confirm Password</label>
                            <input type="password" class="form-control" name="confirm-password" placeholder="Password" id="confirm-password" required />
                        </div>
                        <div class="form-group mb-2">
                            <input type="submit" class="btn btn-primary" value="Register"></input>
                            <p class="text-danger" id="warnings"><?php
                            if (isset($warnings)) {
                                echo $warnings;
                            } ?></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <script type="application/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
        <script type="application/javascript" src="/js/setup.js"></script>
    </body>
</html>