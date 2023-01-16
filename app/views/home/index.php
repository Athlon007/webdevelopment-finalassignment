<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#fffbfa">
    <meta name="description" content="Share your thoughts on topics picked every 24 hours!">
    <meta name=”robots” content="index, follow">
    <title>My Opinion</title>
    <link rel="stylesheet" type="text/css" href="/css/styles.css" />
</head>

<body>
    <section class="overlay" id="input-opinion-panel">
        <section class="overlay-content" id="input-opinion-panel-content">
            <button class="btn-overlay-close">X</button>
            <h2>Submit</h2>
            <form method="POST">
                <label for="title-input">Title</label>
                <input type="text" name="title" id="title-input" oninput="validateTitleInput();" maxlength="32" required>
                <label class="input-char-counter" id="title-char-counter">0/0</label>
                <label for="content-input">What are your thoughts?</label>
                <textarea name="content" id="content-input" required maxlength="512" oninput="validateContentInput();"></textarea>
                <label class="input-char-counter" id="content-char-counter">0/0</label>
                <label for="not-a-robot">
                    <input type="checkbox" value="Pinky promise that I'm not a robot" id="not-a-robot" required>
                    Pinky promise that I'm not a robot!
                </label>
                <input id="btn-submit-opinion" class="emoji" type="button">
            </form>
            <p id="warning-opinion" class="warning">Warning</p>
        </section>
    </section>
    <section class="overlay" id="input-report-panel">
        <section class="overlay-content" id="input-opinion-panel-content">
            <button class="btn-overlay-close">X</button>
            <h2>Report abuse</h2>
            <form method="POST">
                <div class="radio-block" id="report-types">
                </div>
                <input id="btn-submit-report" type="button" value="Report">
            </form>
            <p id="warning-report" class="warning">Warning</p>
        </section>
    </section>
    <section class="popup" id="reaction-panel">
        <header>React!</header>
        <main id="reactions">
            <input type="hidden" name="action" value="reaction" />
            <input type="hidden" id="opinion-reaction" name="opinionReaction" value="" />
        </main>
    </section>
    <header>
        <h1>Today's topic is...</h1>
        <h2 class="topic" id="topic"></h2>
    </header>
    <section class="center">
        <button id="btn-show-opinion-panel" class="btn-write-topic">
            <p class="emoji">&#9997;</p> Write opinion
        </button>
    </section>
    <section>
        <div class="radio-list">
            <label>
                <input id="sort-by-popular" type="radio" name="sortby" value="popular">
                Popular
                </input>
            </label>
            <label>
                <input id="sort-by-new" type="radio" name="sortby" value="new">
                New
                </input>
            </label>
            </form>
    </section>
    <section class="opinions" id="opinions">

    </section>
    <nav class="pages" id="pages">
    </nav>
    <footer class="foot">
        <ul>
            <li>
                <a href="/404">404</a>
            </li>
            <li>
                <a href="/admin">Admin Login</a>
            </li>
        </ul>
        <hr>
        <p>
            Copyright &copy; 2023 <a href="http://kfigura.nl">Konrad Figura</a>
        </p>
    </footer>
    <script type="application/javascript" src="/js/index.js"></script>
</body>

</html>