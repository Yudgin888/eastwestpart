<?php

/* @var $this yii\web\View */

$this->title = 'Создание КП | Выбор модели';
?>
<div class="site-index">
    <div class="body-content">
        <?php
            $id = Yii::$app->request->get('id');
            $models = \app\models\TModel::find()->asArray()->where('id_category=' . $id)->all();
            $cats = \app\models\Category::find()->asArray()->where('id=' . $id)->all();
            $breadcrumbs[] = $title = $cats['0']['name'];
            while($cats['0']['id_par'] != 0){
                $cats = \app\models\Category::find()->asArray()->where('id=' . $cats['0']['id_par'])->all();
                $breadcrumbs[] = $cats['0']['name'];
            }
            //var_dump($cats);
        ?>
        <ol class="breadcrumb">
            <?php foreach (array_reverse($breadcrumbs) as $item):?>
            <li class="active"><?= $item?></li>
            <?php endforeach;?>
        </ol>
        <h1><?= $title?></h1>
        <?php foreach ($models as $model):?>
        <div class="model-item">
            <h2>- Модель <?= $model['name']?></h2>
            <div class="btn-load"><b>КП</b> - Характеристики, базовая информация и перечень доступных опций.</div>
            <div class="model-content">
                <div class="model-options">
                    <h2>Опции</h2>
                    <label>
                        <input type="checkbox" value="1" />  Название опции</label>
                    <label>
                        <input type="checkbox" value="1" />  Название опции</label>
                    <label>
                        <input type="checkbox" value="1" />  Название опции</label>
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