<!DOCTYPE>
<html lang="en">
    <head>
        <title>My Opinion - Login</title>
        <link rel="stylesheet" href="css/styles.css"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    </head>
    <body>
        <header>
            <h1>Setup</h1>
        </header>
        <section class="login-form">
            <article class="opinion">
                <h3>Create first admin account...</h3>
                <form method="POST">
                    <label>Login</label>
                    <input type="text" name="login" required/>
                    <label>Password</label>
                    <input type="password" name="password" required/>
                    <label>Repeat password</label>
                    <input type="password" name="password-repeat" required/>
                    <input type="submit" value="Create"/>
                </form>
            </article>
        </section>
    </body>
</html>
