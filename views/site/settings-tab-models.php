<?php

use yii\bootstrap\ActiveForm;

include 'settings-header.php';
?>
    <div class="setting-tab-item">
        <h2>Модели: (<?= $count_mod?>)</h2>
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
        <div class="container models-container">
            <p>Перед загрузкой коммерческих предложений для начала загрузите список категорий и моделей (КМ
                привязывается к определенной модели)</p>
            <?php if ($count_mod == 0): ?>
                <a href="/settings?tab=upload">Загрузка данных</a>
            <?php else: ?>
                <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>
                <?= $form->field($multiupload, 'files[]')->fileInput(['multiple' => true, 'accept' => '*.pdf'])->label('Загрузка КМ (файлы pdf): ') ?>
                <button>Отправить</button>
                <?php ActiveForm::end() ?>

                <?php foreach ($models as $model):
                    $cats = \app\models\Category::find()->asArray()->where('id=' . $model['id_category'])->all(); ?>
                    <div class="model-item" data-id="<?= $model['id'] ?>"
                         data-iso="<?= (empty($model['offer_path']) ? '0' : '1') ?>">
                        <hr>
                        <div class="tab1">
                            <ol class="breadcrumb">
                                <li class="active"><?= $cats['0']['num'] ?> <?= $cats['0']['name'] ?></li>
                            </ol>
                            <h2>Модель: <?= $model['name'] ?></h2>
                            <?= \yii\helpers\Html::submitButton('', ['class' => 'btn-del-model btn btn-danger glyphicon glyphicon-remove', 'name' => 'del-button', 'title' => 'Удалить модель']) ?>
                            <?= \yii\helpers\Html::submitButton('', ['class' => 'btn-edit-open btn btn-primary glyphicon glyphicon-pencil', 'name' => 'edit-button', 'title' => 'Редактировать']) ?>
                            <label class="delivery">Условия оплаты и доставки:
                                <p><?= htmlspecialchars_decode(stripslashes($model['delivery'])) ?></p></label>
                        </div>

                        <div class="tab2">
                            <label>Категория:</label>
                            <select class="form-control">
                                <?php foreach ($linecategories as $cat): ?>
                                    <option <?= ($model['id_category'] == $cat['id'] ? 'selected' : '') ?>
                                            value="<?= $cat['id'] ?>"><?= str_repeat('-', intval($cat['lvl'])) . $cat['num'] . ' ' . $cat['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label>Модель: <input class='input-edit-name form-control' type="text"
                                                  value="<?= $model['name'] ?>"></label>
                            <label for="txt-area-delvr<?= $model['id'] ?>">Условия оплаты и доставки:</label>
                            <textarea id="txt-area-delvr<?= $model['id'] ?>" class="txt-area-delvr form-control"
                                      name="txt-area-del"><?= htmlspecialchars_decode(stripslashes($model['delivery'])) ?></textarea>
                            <?= \yii\helpers\Html::submitButton('', ['class' => 'btn-save-delvr btn btn-success glyphicon glyphicon-ok', 'name' => 'save-button', 'title' => 'Сохранить']) ?>
                            <?= \yii\helpers\Html::submitButton('', ['class' => 'btn-edit-close btn btn-danger glyphicon glyphicon-remove', 'name' => 'close-button', 'title' => 'Отмена']) ?>
                        </div>

                        <div>
                            <div class="edit-model-left">
                                <?php if (!empty($model['offer_path'])): ?>
                                    <div>
                                        <p>Загруженное коммерческое предложение: </p>
                                        <a href="<?= '/' . $model['offer_path'] ?>"
                                           target="_blank"><?= $model['offer_path'] ?></a>
                                    </div>
                                <?php endif; ?>
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
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <div id="dialog" style="display:none" title="Удаление модели"></div>

<?php
include 'settings-footer.php';
?>