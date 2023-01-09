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
    <?php
    if ($topic == null) {
        echo "<h1>Oi', Admin!</h1>";
        echo "<h2>Set the damn page first!</h2>";
        echo "<button style='width: 100%' onclick='window.location.href = `/admin/topics`'>Go to admin panel</button>";
        return;
    }
    if (isset($_GET["message"])) {
    ?>
        <div class="popup dismisable popup-success">
            <button class="btn-overlay-close">X</button>
            <header>Alert</header>
            <main><?= $_GET["message"] ?></main>
        </div>
    <?php } ?>
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
                <input type="hidden" name="topicID" value="<?= $topic->getId() ?>" />
                <input id="btn-submit-opinion" class="emoji" type="button" value="Send!">
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
                    <?php
                    foreach ($reportTypes as $reportType) {
                    ?>
                        <label>
                            <input type="radio" id="report-type-<?= $reportType->value ?>" name="reportType" value="<?= $reportType->value ?>" />
                            <?= $reportType->asString() ?>
                        </label>
                    <?php
                    } ?>
                </div>
                <input id="btn-submit-report" type="button" value="Report">
            </form>
            <p id="warning-report" class="warning">Warning</p>
            <p id="success-report" class="success">Success</p>
        </section>
    </section>
    <section class="popup" id="reaction-panel">
        <header>React!</header>
        <main>
            <?php
            foreach ($reactionEntites as $reactionEntity) {
            ?>
                <button class="reaction emoji" onclick="addNewReactionToOpinion(<?= $reactionEntity->getId() ?>)">
                    <?= $reactionEntity->getHtmlEntity() ?>
                </button>
            <?php
            }
            ?>
            <input type="hidden" name="action" value="reaction" />
            <input type="hidden" id="opinion-reaction" name="opinionReaction" value="" />
        </main>
    </section>
    <header>
        <h1>Today's topic is...</h1>
        <h2 class="topic"><?= $topic->getName() ?></h2>
    </header>
    <section class="center">
        <button onclick="showOpinionPanel();" class="btn-write-topic">
            <p class="emoji">&#9997;</p> Write opinion
        </button>
    </section>
    <section>
        <form class="radio-list" method="GET" id="sort-by-form">
            <label>
                <input id="sort-by-popular" type="radio" name="sortby" value="popular" onclick="this.form.submit();" <?php if ($sortby == "popular") {
                                                                                                                            echo "checked";
                                                                                                                        } ?>>
                Popular
                </input>
            </label>
            <label>
                <input id="sort-by-new" type="radio" name="sortby" value="new" onclick="this.form.submit();" <?php if ($sortby == "new") {
                                                                                                                    echo "checked";
                                                                                                                } ?>>
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
            Copyright &copy; 2022 <a href="http://kfigura.nl">Konrad Figura</a>
        </p>
    </footer>
    <script type="application/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    <script type="application/javascript" src="/js/index.js"></script>
</body>

</html>