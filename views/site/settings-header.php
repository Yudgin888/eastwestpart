<?php
$this->title = 'Создание КП | Настройки';
?>
<div class="site-index">
    <?php
    $success = [
        '0' => 'success-load',
        '1' => 'success-proc',
        '2' => 'success-del-user',
        '3' => 'success-add-user',
        '4' => 'success-parse-cost',
        '5' => 'success-parse-cost-count',
    ];
    $error = [
        '0' => 'error-load',
        '1' => 'error-proc',
        '2' => 'error-del-user',
        '3' => 'error-parse-cost',
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
                $act_tab = 'upload-price';
            }
        ?>
        <ul class="nav nav-tabs settings-tabs">
            <li data-tag="upload-price" class="<?= ($act_tab == 'upload-price') ? 'active' : ''?>"><a href="?tab=upload-price">Загрузка прайса</a></li>
            <li data-tag="users" class="<?= ($act_tab == 'users') ? 'active' : ''?>"><a href="?tab=users">Пользователи</a></li>
            <li data-tag="upload-cost" class="<?= ($act_tab == 'upload-cost') ? 'active' : ''?>"><a href="?tab=upload-cost">Загрузка опций</a></li>
            <li data-tag="upload-kpm" class="<?= ($act_tab == 'upload-kpm') ? 'active' : ''?>"><a href="?tab=upload-kpm">Загрузка КП моделей</a></li>
        </ul>

