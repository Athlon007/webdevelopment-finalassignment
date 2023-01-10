<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#fffbfa">
    <meta name="robots" content="noindex, nofollow">
    <title>My Opinion - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/css/all.min.css" rel="stylesheet">
    <link href="/css/admin-styles.css" rel="stylesheet">
</head>

<body>
    <div class="modal" tabindex="-1" role="dialog" id="confirm-remove">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Warning</h4>
                    <button id="confirm-remove-close" type="button" class="btn close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="d-inline">Are you sure you want to delete the opinion ID
                    <p id="confirm-remove-id" class="font-weight-bold d-inline">-1</p>?</p>
                    <p>This operation is irreversible!</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="modal-btn-yes">Yes</button>
                    <button type="button" class="btn btn-primary" id="modal-btn-no">No</button>
                </div>
            </div>
        </div>
    </div>
    <nav class="navbar navbar-expand-md navbar-dark bg-dark" aria-label="Seventh navbar example">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">My Opinion - Admin</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-data" aria-controls="navbar-data" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbar-data">
                <ul class="navbar-nav me-auto mb-2 mb-xl-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#">Opinions</a>
                    </li>
                    <?php if ($activeUser->getAccountType() == AccountType::Admin) { ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/admin/topics">Topics</a>
                        </li>
                    <?php } ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/reports">Reports</a>
                    </li>
                    <?php if ($activeUser->getAccountType() == AccountType::Admin) { ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/admin/reactions">Reactions</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/admin/users">Users</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/admin/settings">Settings</a>
                        </li>
                    <?php } ?>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                            &#91;<?= $activeUser->getID() ?>, <?= $activeUser->getAccountType()->name ?>&#93; <?= $activeUser->getUsername(); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" id="btn-logout">Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container">
        <?php
        if (strlen($warnings) > 0) {
        ?>
            <div class="alert alert-danger alert-dismissible m-2" role="alert">
                <?= $warnings ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
            </div>
        <?php } ?>
        <div class="row mt-2">
            <div class="col-md-8">
                <div class="card p-2">
                    <h2>Opinions</h2>
                    <form id="pick-topic" class="m-2">
                        <select class="form-select" name="topic" onchange="this.form.submit()">
                            <option disabled selected value> -- Select an option -- </option>
                            <?php
                            foreach ($topics as $topic) {
                            ?>
                                <option <?php if ($topic->getId() == $currentTopic) {
                                            echo "selected";
                                        } ?> value="<?= $topic->getId() ?>"><?= $topic->getName() ?></option>
                            <? } ?>
                        </select>
                    </form>
                    <div class="m-2">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="col-1">ID</th>
                                    <th class="col-3">Title</th>
                                    <th>Content</th>
                                    <th class="col-2">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($opinions as $opinion) {
                                ?>
                                    <tr id="opinion-<?= $opinion->getId() ?>">
                                        <td><?= $opinion->getId() ?></td>
                                        <td class="text-wrap text-break"><?= $opinion->getTitle() ?></td>
                                        <td class="text-wrap text-break" class="width: 6rem"><?= $opinion->getContent() ?></td>
                                        <td>
                                            <button onclick="startEditorForOpinion(<?= $opinion->getId() ?>);" class="btn btn-primary m-1 w-100">Edit</button>
                                            <button onclick="deleteOpinionById(<?= $opinion->getId() ?>);" class="btn btn-danger m-1 w-100">Delete</button>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-4 overlay-on-small" id="editor">
                <div class="card p-2">
                    <h2>Editor</h2>
                    <form method="POST">
                        <input type="hidden" name="action" value="edit-opinion">
                        <input type="hidden" id="editor-id" name="opinion-id" value="">
                        <div class="form-group m-2">
                            <label for="title">Title</label>
                            <input id="editor-title" maxlength="32" type="text" class="form-control" name="title" disabled>
                        </div>
                        <div class="form-group m-2">
                            <label for="content">Content</label>
                            <textarea id="editor-content" maxlength="512" class="form-control" rows="10" name="content" disabled></textarea>
                        </div>
                        <div class="form-group mt-4 m-2">
                            <input id="btn-edit" class="btn btn-primary" type="submit" value="Update" disabled>
                            <button type="button" onclick="clearEditor();" class="btn btn-secondary">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script type="application/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <script type="application/javascript" src="/js/admin-global.js"></script>
    <script type="application/javascript" src="/js/admin-panel.js"></script>
</body>

</html>