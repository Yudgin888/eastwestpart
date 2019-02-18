<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => 'Главная',
        'brandUrl' => '/',
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => [
            (!Yii::$app->user->isGuest && Yii::$app->user->identity->getRole() === MANAGER && !empty($agency = \app\models\Agency::findOne(['id' => Yii::$app->user->identity->getAgencyId()]))) ? (
            ['label' => substr($agency->name, 0, 100)]
            ) : '',
            (!Yii::$app->user->isGuest && Yii::$app->user->identity->getRole() == ADMIN) ? (
                ['label' => 'Настройки', 'url' => ['/site/settings']]
            ) : '',
            Yii::$app->user->isGuest ? (
                ['label' => 'Войти', 'url' => ['/site/login']]
            ) : (
                '<li>'
                . Html::beginForm(['/site/logout'], 'post')
                . Html::submitButton(
                    'Выйти (' . Yii::$app->user->identity->username . ')',
                    ['class' => 'btn btn-link logout']
                )
                . Html::endForm()
                . '</li>'
            )
        ],
    ]);
    NavBar::end();
    ?>

    <div class="container">
<!--        --><?//= Breadcrumbs::widget([
//            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
//        ]) ?>
        <?= Alert::widget() ?>
        <?php
        $success = [
            '0' => 'success-load',
            '1' => 'success-load-2',
            '2' => 'success-parse-cost',
            '3' => 'success-parse-cost-count',
            '4' => 'success-proc',
        ];
        $error = [
            '0' => 'error-load',
            '1' => 'error-load-2',
            '2' => 'error-parse-cost',
            '3' => 'error-proc',
        ];
        foreach ($success as $item):
            if(Yii::$app->session->hasFlash($item)):?>
                <div class="alert alert-success alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <?= Yii::$app->session->getFlash($item);?>
                </div>
            <?php endif;?>
        <?php endforeach;?>
        <?php foreach ($error as $item):
            if(Yii::$app->session->hasFlash($item)):?>
                <div class="alert alert-danger alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <?= Yii::$app->session->getFlash($item);?>
                </div>
            <?php endif;?>
        <?php endforeach;?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left"><a href="https://www.eastwestpart.ru/">eastwestpart.ru</a> <?= date('Y') ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
