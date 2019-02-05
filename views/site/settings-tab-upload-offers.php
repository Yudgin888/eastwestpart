<?php
use yii\bootstrap\ActiveForm;
include 'settings-header.php';
?>
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
            <?php if($count_mod == 0):?>
                <p>Перед загрузкой коммерческих предложений для начала загрузите список категорий и моделей (КМ привязывается к определенной модели)</p>
                <a href="/settings?tab=upload-price">Загрузка прайса</a>
            <?php else:?>
                <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>
                <?= $form->field($multiupload, 'files[]')->fileInput(['multiple' => true, 'accept' => '*.pdf'])->label('Загрузка КМ (файлы pdf): ') ?>
                <button>Отправить</button>
                <?php ActiveForm::end() ?>

                <?php foreach ($models as $model):
                    $cats = \app\models\Category::find()->asArray()->where('id=' . $model['id_category'])->all();?>
                    <div class="model-item" data-id="<?= $model['id']?>" data-iso="<?= (empty($model['offer_path']) ? '0' : '1')?>">
                        <hr>
                        <ol class="breadcrumb">
                            <li class="active"><?= $cats['0']['name']?></li>
                        </ol>
                        <h2>Модель: <?= $model['name']?></h2>
                        <div>
                            <div class="edit-model-left">
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
                                    <?= $form->field($uploadmodel, 'file')->fileInput()->label('Загрузить' . (!empty($model['offer_path']) ? ' новое' : '') . ' коммерческое предложение (файл pdf): ') ?>
                                    <?= $form->field($uploadmodel, 'hidden1')->hiddenInput(['value' => $model['id']])->label(false); ?>
                                    <button>Отправить</button>
                                    <?php ActiveForm::end() ?>
                                </div>
                            </div>
                            <div class="edit-model-right">
                                <label for="txt-area-delvr<?= $model['id']?>">Условия оплаты и доставки:</label>
                                <textarea id="txt-area-delvr<?= $model['id']?>" class="txt-area-delvr" name="txt-area-del"><?= htmlspecialchars_decode(stripslashes($model['delivery']))?></textarea>
                                <button class="btn-save-delvr">Сохранить</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach;?>
            <?php endif;?>
        </div>
    </div>

<?php
include 'settings-footer.php';
?>