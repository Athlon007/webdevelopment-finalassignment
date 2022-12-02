<!DOCTYPE>
<html lang="en">
    <head>
        <title>My Opinion - Admin Topics Panel</title>
        <link rel="stylesheet" href="css/styles.css"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    </head>
    <body>
        <nav class="admin-panel-user-manage">
            <p>Username (Admin)</p>
            <a>Logout</a>
        </nav>
        <header>
            <h1>Admin Panel - Topics</h1>
        </header>
        <nav class="admin-nav">
            <button class="btn-secondary" onclick="location.href='admin.php'">Opinions</button>
            <button>Topics</button>
            <button class="btn-secondary" onclick="location.href='admin-users.php'">Users</button>
        </nav>
        <div class="admin-controls">
            <main>
                <table>
                    <tr>
                        <th>ID</th>
                        <th>Topic</th>
                        <th>Actions</th>
                    </tr>
                    <tr>
                        <td>1</td>
                        <td>Apple</td>
                        <td>
                            <button>Set Active</button>
                            <button>Edit</button>
                            <button>Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Topic 2</td>
                        <td>
                            <button>Edit</button>
                            <button>Delete</button>
                        </td>
                    </tr>
                </table>
                <nav class="page">
                    <button class="btn">1</button>
                    <button class="btn-secondary">2</button>
                    <button class="btn-secondary">2</button>
                </nav>
            </main>
            <aside class="edit-post">
                <h3>Edit</h3>
                <p class="inline">Currently editing ID:</p>
                <p id="editing-message-id" class="inline">1</p>
                <form>
                    <label>Message</label>
                    <textarea id="edit-message"></textarea>
                    <input type="submit" value="Apply"/>
                </form>
            </aside>
        </div>
        <footer class="foot">
            <p>
                Copyright &copy; 2022 <a href="http://kfigura.nl">Konrad Figura</a>
            </p>
        </footer>
    </body>
</html>
