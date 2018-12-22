<?php
use yii\bootstrap\ActiveForm;
include 'settings-header.php';
?>
<?php// var_dump($models); ?>

    <div class="setting-tab-item">
        <h2>Модели:</h2>
        <ul class="nav nav-pills">
            <li class="nav-item">
                <a class="nav-link-sort active" data-iso="2" href="#">Все</a>
            </li>
            <li class="nav-item">
                <a class="nav-link-sort" data-iso="1" href="#">С КП</a>
            </li>
            <li class="nav-item">
                <a class="nav-link-sort" data-iso="0" href="#">Без КП</a>
            </li>
        </ul>
        <div class="container">

            <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>
            <?= $form->field($multiupload, 'files[]')->fileInput(['multiple' => true, 'accept' => '*.pdf'])->label('Загрузка КМ (файлы pdf): ') ?>
            <button>Отправить</button>
            <?php ActiveForm::end() ?>

        <?php foreach ($models as $model):
            $cats = \app\models\Category::find()->asArray()->where('id=' . $model['id_category'])->all();?>
            <div class="model-item" data-iso="<?= (empty($model['offer_path']) ? '0' : '1')?>">
                <hr>
                <ol class="breadcrumb">
                    <li class="active"><?= $cats['0']['name']?></li>
                </ol>
                <h2>Модель: <?= $model['name']?></h2>
                <?php if(!empty($model['offer_path'])):?>
                <div>
                    <p>Загруженное коммерческое предложение: </p>
                    <a href="<?= '/' . $model['offer_path']?>" target="_blank"><?= $model['offer_path']?></a>
                </div>
                <?php endif;?>
                <div class="upload-offer">
                    <?php $form = ActiveForm::begin([
                        'options' => [
                            'enctype' => 'multipart/form-data',
                        ],
                    ]) ?>
                    <?= $form->field($uploadmodelkm, 'file')->fileInput()->label('Загрузка коммерческого предложения (файлы pdf): ') ?>
                    <?= $form->field($uploadmodelkm, 'hidden1')->hiddenInput(['value' => $model['id']])->label(false); ?>
                    <button>Отправить</button>
                    <?php ActiveForm::end() ?>
                </div>
            </div>
        <?php endforeach;?>
        </div>
    </div>

<?php
include 'settings-footer.php';
?>