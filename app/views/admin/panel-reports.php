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
                    <p class="d-inline">Are you sure you want to delete the user ID
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
    <nav class="navbar navbar-expand-md navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">My Opinion - Admin</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-data" aria-controls="navbar-data" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbar-data">
                <ul class="navbar-nav me-auto mb-2 mb-xl-0">
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="/admin">Opinions</a>
                    </li>
                    <?php if ($activeUser->getAccountType() == AccountType::Admin) { ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/admin/topics">Topics</a>
                        </li>
                    <?php } ?>
                    <li class="nav-item">
                        <a class="nav-link active" href="#">Reports</a>
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
            <div class="col-md-12">
                <div class="card p-2">
                    <h2>Reports</h2>
                    <div class="m-2">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="col-2">Opinion</th>
                                    <th class="col-1">Topic</th>
                                    <?php
                                    foreach ($reportTypes as $reportType) {
                                    ?>
                                        <th class="col-1 text-wrap text-break"><?= $reportType->name ?></th>
                                    <?
                                    }
                                    ?>
                                    <th class="col-2">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($opinionsWithReportsCount as $opinion) {
                                ?>
                                    <tr>
                                        <td class="text-break text-wrap"><?= $opinion["opinion"]->getContent() ?></td>
                                        <td><?= $opinion["opinion"]->getTopic()->getName() ?></td>
                                        <?php
                                        foreach ($reportTypes as $reportType) {
                                        ?>
                                            <td><?= $opinion[$reportType->name] ?></td>
                                        <?php
                                        } ?>
                                        <td>
                                            <button onclick="dismissReport(<?= $opinion["opinion"]->getId(); ?>);" class="btn btn-primary m-1 w-100">Dismiss</button>
                                            <button onclick="deleteOpinion(<?= $opinion["opinion"]->getId(); ?>);" class="btn btn-danger m-1 w-100">Delete Opinion</button>
                                        </td>
                                    </tr>
                                <?php
                                } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <script type="application/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <script type="application/javascript" src="/js/admin-global.js"></script>
    <script type="application/javascript" src="/js/admin-reports.js"></script>
</body>

</html>