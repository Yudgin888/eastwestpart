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
        <div class="model-item model-item-main" data-id="<?= $model['id']?>">
            <h2>- Модель <?= $model['name']?></h2>
            <?php if(!empty($model['offer_path'])):?>
                <button class="btn-open-offer" title="Открыть pdf"><b>КП</b> - Характеристики, базовая информация<br> и перечень доступных опций</button>
            <?php else:?>
                <button class="btn-no-basic-pdf" title="На страницу загрузки">Для модели не загружен базовый pdf-файл</button>
            <?php endif;?>

            <div class="model-content">
                <div class="model-options">
                    <h2>Опции</h2>
                    <?php foreach ($opt as $item):?>
                        <label>
                            <input type="checkbox" data-id="<?= $item['id']?>" value="<?= $item['name']?>" />  <?= $item['name']?> - $<?= $item['cost']?></label>
                    <?php endforeach;?>
                    <?php if(count($opt) == 0):?>
                        <label>Нет доступных опций</label>
                    <?php endif;?>
                </div>
                <div class="model-delivery">
                    <h2>Город доставки/<br>ближайший населенный пункт</h2>
                    <div class="city-delivery">
                        <div class="input-group">
                            <input type="text" class="form-control city-input" placeholder="Введите название города">
                            <span class="input-group-btn">
                                <button class="btn btn-default glyphicon glyphicon-search" type="button"></button>
                            </span>
                        </div>
                    </div>
                    <div class="cost-delivery">
                        <input type="text" maxlength="100" onkeypress="return isNumberKey(event)" class="form-control" placeholder="Стоимость доставки, у.е." aria-describedby="basic-addon2">
                    </div>
                </div>
            </div>
            <?php if(!empty($model['offer_path'])):?>
                <button class="btn-load-price" title="Открыть pdf"><b>КП</b> - Характеристики, базовая комплектация,<br> выбранные опции и стоимость</button>
            <?php else:?>
                <button class="btn-no-basic-pdf" title="На страницу загрузки">Для модели не загружен базовый pdf-файл</button>
            <?php endif;?>
        </div>
            <hr>
        <?php endforeach;?>

    </div>
</div>