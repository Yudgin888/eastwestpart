<?php
$this->title = 'Создание КП | Настройки';
?>
<div class="site-index">
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
            <li data-tag="categories" class="<?= ($act_tab == 'categories') ? 'active' : ''?>"><a href="?tab=categories">Категории</a></li>
            <li data-tag="models" class="<?= ($act_tab == 'models') ? 'active' : ''?>"><a href="?tab=models">Модели</a></li>
            <li data-tag="options" class="<?= ($act_tab == 'options') ? 'active' : ''?>"><a href="?tab=options">Опции</a></li>
            <li data-tag="upload" class="<?= ($act_tab == 'upload') ? 'active' : ''?>"><a href="?tab=upload">Загрузка данных</a></li>
            <li data-tag="users" class="<?= ($act_tab == 'users') ? 'active' : ''?>"><a href="?tab=users">Пользователи</a></li>
            <li data-tag="cities" class="<?= ($act_tab == 'cities') ? 'active' : ''?>"><a href="?tab=cities">Населенные пункты</a></li>
            <li data-tag="logs" class="<?= ($act_tab == 'logs') ? 'active' : ''?>"><a href="?tab=logs">Логи</a></li>
        </ul>

