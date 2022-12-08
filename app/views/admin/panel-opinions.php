<!DOCTYPE>
<html lang="en">
    <head>
        <title>My Opinion - Admin Panel</title>
        <link rel="stylesheet" href="css/styles.css"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    </head>
    <body>
        <nav class="admin-panel-user-manage">
            <p>Username (Admin)</p>
            <a>Logout</a>
        </nav>
        <header>
            <h1>Admin Panel - Opinions</h1>
        </header>
        <nav class="admin-nav">
            <button>Opinions</button>
            <button class="btn-secondary" onclick="location.href='admin-topics.php'">Topics</button>
            <button class="btn-secondary" onclick="location.href='admin-users.php'">Users</button>
        </nav>
        <div class="admin-controls">
            <main>
                <form id="pick-topic">
                    <label>Opinions on </label>
                    <select name="topic">
                        <option value="topic1">Topic1</option>
                        <option value="topic1">Topic1</option>
                        <option value="topic1">Topic1</option>
                        <option value="topic1">Topic1</option>
                    </select>
                </form>
                <table>
                    <tr>
                        <th>ID</th>
                        <th>Message</th>
                        <th>Date Time</th>
                        <th>Actions</th>
                    </tr>
                    <tr>
                        <td>1</td>
                        <td>Lorem ipsum dolor sit amet.</td>
                        <td>2022-11-26 13:54</td>
                        <td>
                            <button>Edit</button>
                            <button>Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque scelerisque pharetra lacus at ullamcorper.Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque scelerisque pharetra lacus at ullamcorper.Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque scelerisque pharetra lacus at ullamcorper.</td>
                        <td>2022-11-26 13:54</td>
                        <td>
                            <button>Edit</button>
                            <button>Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque scelerisque pharetra lacus at ullamcorper.Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque scelerisque pharetra lacus at ullamcorper.Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque scelerisque pharetra lacus at ullamcorper.</td>
                        <td>2022-11-26 13:54</td>
                        <td>
                            <button>Edit</button>
                            <button>Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque scelerisque pharetra lacus at ullamcorper.Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque scelerisque pharetra lacus at ullamcorper.Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque scelerisque pharetra lacus at ullamcorper.</td>
                        <td>2022-11-26 13:54</td>
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
