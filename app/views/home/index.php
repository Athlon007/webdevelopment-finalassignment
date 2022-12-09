<!DOCTYPE>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
        <title>My Opinion</title>
        <link rel="stylesheet" href="css/styles.css"/>
    </head>
    <body>
        <section id="input-opinion-panel">
            <section id="input-opinion-panel-content">
                <button class="button" onclick="hideOpinionPanel();" id="input-opinion-hide">X</button>
                <h2>Submit</h2>
                <form method="POST">
                    <label for="title">Title</label>
                    <input type="text" name="title" id="title-input" oninput="validateTitleInput();" maxlength="32" required>
                    <label class="input-char-counter" id="title-char-counter">0/0</label>
                    <label for="content">What are your thoughts?</label>
                    <textarea name="content" id="content-input" required maxlength="512" oninput="validateContentInput();"></textarea>
                    <label class="input-char-counter" id="content-char-counter">0/0</label>
                    <input type="hidden" name="topicID" value="<?= $topic->getId() ?>"/>
                    <input type="submit">
                </form>
            </section>
        </section>
        <section id="reaction-panel">
            <h3>React!</h3>
            <section">
                <?php
                foreach ($reactionEntites as $reactionEntity) {
                    ?>
                    <button class="reaction" onclick="addNewReactionToOpinion(<?= $reactionEntity->getId()?>)"><?= $reactionEntity->getHtmlEntity() ?></button>
                <?php
                }
                ?>
                <input type="hidden" name="action" value="reaction"/>
                <input type="hidden" id="opinion-reaction" name="opinionReaction" value=""/>
            </section>
        </section>
        <header>
            <h1>Today's topic is...</h1>
            <h2 class="topic"><?= $topic->getName() ?></h2>
        </header>
        <section class="section-write-opinion-button">
            <button onclick="showOpinionPanel();" class="btn-write-topic"><p class="emoji">&#9997</p> Write opinion</button>
        </section>
        <section>
            <form class="sort-by-options" method="GET">
                <label>
                    <input type="radio" name="sortby" value="popular" onclick="this.form.submit();" <?php if ($sortby == "popular") echo "checked" ?> >
                        Popular
                    </input>
                </label>
                <label>
                    <input type="radio" name="sortby" value="new" onclick="this.form.submit();" <?php if ($sortby == "new") echo "checked" ?>>
                        New
                    </input>
                </label>
            </form>
        </section>
        <section class="opinions">
            <?php
            foreach ($opinions as $opinion) {
            ?>
            <article class="opinion">
                <header><?= $opinion->getTitle() ?></header>
                <main><?= $opinion->getContent() ?></main>
                <section class="reactions">
                    <?php
                    foreach ($opinion->getAllReactions() as $reaction) {
                    ?>
                    <button class="reaction" onclick="increaseExistingOpinionCount(<?= $opinion->getId() ?>, <?= $reaction->getId() ?>)">
                        <p class="emoji">
                            <?= $reaction->getReactionEntity()->getHtmlEntity() ?>
                        </p> <?= $reaction->getCount() ?>
                    </button>
                    <?php } ?>
                    <button class="reaction btn-secondary"
                    id="button-add-reaction-<?= $opinion->getId() ?>"
                    onclick="showReactionPanel(<?= $opinion->getId() ?>);">+</button>
                </section>
                <a class="report-issue">Report...</a>
            </article>
            <?php } ?>
        </section>
        <nav class="page">
            <?php
            for ($i = 0; $i < $pagesCount; $i++) {
            ?>
                <button class=
                "<?php echo (($i + 1 == $currentPage) ? 'btn' : 'btn-secondary') ?>"
                ><?= strval($i + 1) ?></button>
            <?php
            }
            ?>
        </nav>
        <footer class="foot">
            <p>
                Copyright &copy; 2022 <a href="http://kfigura.nl">Konrad Figura</a>
            </p>
        </footer>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
        <script src="js/index.js"></script>
    </body>
</html>
