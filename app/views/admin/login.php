<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#fffbfa">
    <meta name="robots" content="noindex, nofollow">
    <title>My Opinion - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="/css/login.css">
</head>

<body>
    <div class="container">
        <div class="row align-items-center h-100">
            <div class="col-sm-12 col-md-6 mx-auto p-4">
                <h1>Login</h1>
                <form method="POST">
                    <div class="form-group mt-2 mb-2">
                        <input type="text" class="form-control" name="email" placeholder="E-mail or Username" value="<?= isset($_POST["email"]) ? $_POST["email"] : "" ?>" required />
                    </div>
                    <div class="form-group mb-2">
                        <input type="password" class="form-control" name="password" placeholder="Password" value="" required />
                    </div>
                    <div class="form-group mb-2">
                        <input type="submit" class="btn btn-primary" value="Login" />
                    </div>
                    <div class="form-group mb-2">
                        <p class="text-danger" id="warnings">
                            <?php
                            if (isset($warnings)) {
                                echo $warnings;
                            } ?>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script type="application/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
</body>

</html>