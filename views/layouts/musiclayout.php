<?php

use app\assets\MusicAsset;
use yii\helpers\Url;

MusicAsset::register($this);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['http-equiv' => 'X-UA-Compatible', 'content' => 'IE=edge']);

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>

<html lang="<?= Yii::$app->language ?>">
    <head>
    
        <title><?= $this->title ?></title>

        <?php $this->head() ?>
    
    </head>
    <body page="1">
        <?php $this->beginBody() ?>
        <nav class="navbar navbar-expand-lg text-bg-primary">
            <div class="container-fluid container-md">
                <a class="navbar-brand fw-bold" href="/">Music We!</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarText">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link text-light" href="<?=Url::to(['@upload'])?>">Добавить трек</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-light" href="<?=Url::to(['@auth'])?>">
                                <?php if(Yii::$app->user->isGuest) : ?>
                                    Войти в аккаунт
                                <?php else : ?>
                                    Выйти из аккаунт
                                <?php endif; ?>
                            </a>
                        </li>
                    </ul>
                    <span class="navbar-text text-light">
                        Task!
                    </span>
                </div>
            </div>
        </nav>

        <div class="container-md bg-light">
            <div class="container text-center p-3">
                <?= $content ?>
            </div>
        </div>

        <div class="modal fade" id="Error" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="staticBackdropLabel">Упс !</h1>
                        <button type="button" hidden="true" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div id="TextError" class="modal-body">
                        Походу данные с треками, по каким-то причинам не могут быть загружены.
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Ok</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="Wait" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="staticBackdropLabel">Внимание!</h1>
                        <button type="button" hidden="true" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Данные обрабатывается пожалуйста подождите!
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Ok</button>
                    </div>
                </div>
            </div>
        </div>

        <?php $this->endBody() ?>

    </body>
</html>

<?php $this->endPage() ?>