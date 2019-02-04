<?php
$this->title = 'Создание КП | Настройки';
?>
<div class="site-index">
    <?php
    $success = [
        '0' => 'success-load',
        '1' => 'success-proc',
        '2' => 'success-parse-cost',
        '3' => 'success-parse-cost-count',
    ];
    $error = [
        '0' => 'error-load',
        '1' => 'error-proc',
        '2' => 'error-parse-cost',
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

    <div class="body-content">
        <h1>Настройки</h1>
        <?php
            $act_tab = Yii::$app->request->get('tab');
            if(empty($act_tab)){
                $act_tab = 'agencys';
            }
        ?>
        <ul class="nav nav-tabs settings-tabs">
            <li data-tag="agencys" class="<?= ($act_tab == 'agencys') ? 'active' : ''?>"><a href="?tab=agencys">Представительства</a></li>
            <li data-tag="upload-price" class="<?= ($act_tab == 'upload-price') ? 'active' : ''?>"><a href="?tab=upload-price">Загрузка прайса</a></li>
            <li data-tag="upload-options" class="<?= ($act_tab == 'upload-options') ? 'active' : ''?>"><a href="?tab=upload-options">Загрузка опций</a></li>
            <li data-tag="upload-offers" class="<?= ($act_tab == 'upload-offers') ? 'active' : ''?>"><a href="?tab=upload-offers">Загрузка КП моделей</a></li>
            <li data-tag="upload-epilog" class="<?= ($act_tab == 'upload-epilog') ? 'active' : ''?>"><a href="?tab=upload-epilog">Загрузка эпилога</a></li>
            <li data-tag="users" class="<?= ($act_tab == 'users') ? 'active' : ''?>"><a href="?tab=users">Пользователи</a></li>
            <li data-tag="cities" class="<?= ($act_tab == 'cities') ? 'active' : ''?>"><a href="?tab=cities">Населенные пункты</a></li>
        </ul>

