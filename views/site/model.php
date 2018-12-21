<?php

/* @var $this yii\web\View */

$this->title = 'Создание КП | Выбор модели';
?>
<div class="site-index">
    <div class="body-content">
        <?php
            //var_dump($cats);
        ?>
        <ol class="breadcrumb">
            <?php foreach (array_reverse($breadcrumbs) as $item):?>
            <li class="active"><?= $item?></li>
            <?php endforeach;?>
        </ol>
        <h1><?= $title?></h1>
        <?php foreach ($models as $model):
            $opt = $model['option'];?>
        <div class="model-item">
            <h2>- Модель <?= $model['name']?></h2>
            <div data-id="<?= $model['id']?>" class="btn-load"><b>КП</b> - Характеристики, базовая информация и перечень доступных опций.</div>
            <div class="model-content">
                <div class="model-options">
                    <h2>Опции</h2>
                    <?php $count = 0; foreach ($opt as $item):?>
                        <?if($item['basic'] == 0): $count++;?>
                        <label>
                            <input type="checkbox" value="<?= $item['name']?>" />  <?= $item['name']?> - $<?= $item['cost']?></label>
                        <?php endif;?>
                    <?php endforeach;?>
                    <?php if($count == 0):?>
                        <label>Нет доступных опций</label>
                    <?php endif;?>
                </div>
                <div class="model-delivery">
                    <h2>Город доставки/<br>ближайший населенный пункт</h2>
                    <div class="city-delivery">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Введите название города">
                            <span class="input-group-btn">
                                <button class="btn btn-default" type="button">Go!</button>
                            </span>
                        </div>
                    </div>
                    <div class="cost-delivery">
                        <input type="text" class="form-control" placeholder="Стоимость доставки, руб." aria-describedby="basic-addon2">
                    </div>
                </div>
            </div>
            <div class="btn-load-price"><b>КП</b> - Характеристики, базовая информация и выбранные опции и стоимость.</div>
        </div>
            <hr>
        <?php endforeach;?>

    </div>
</div>