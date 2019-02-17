<?php
use yii\bootstrap\ActiveForm;
include 'settings-header.php';
?>

<div class="setting-tab-item">
    <h2>Представительства:</h2>

    <div class="container">
        <?php if(count($agencys) == 0):?>
            <p>Не найдено ни одного представительства!</p>
        <?php else:?>
            <?php foreach ($agencys as $agency):?>
                <hr>
                <div class="model-item agency-block" data-id="<?= $agency['id']?>">
                    <div class="agency-name">
                        <div class="tab1">
                            <p class="title"><?= (!empty($agency['name']) ? $agency['name'] : 'Название представительства не задано')?></p>
                        </div>
                        <div class="tab2">
                            <input class='input-edit-name' type="text" value="<?= $agency['name']?>" >
                            <?= \yii\helpers\Html::submitButton('', ['class' => 'btn-save-agency btn btn-success glyphicon glyphicon-ok', 'name' => 'save-button', 'title' => 'Сохранить']) ?>
                            <?= \yii\helpers\Html::submitButton('', ['class' => 'btn-edit-close btn btn-danger glyphicon glyphicon-remove', 'name' => 'close-button', 'title' => 'Отмена']) ?>
                        </div>

                    </div>
                    <?= \yii\helpers\Html::submitButton('', ['class' => 'btn-del-agency btn btn-danger glyphicon glyphicon-remove', 'name' => 'del-button', 'title' => 'Удалить представительство']) ?>
                    <?= \yii\helpers\Html::submitButton('', ['class' => 'btn-edit-open btn btn-primary glyphicon glyphicon-pencil', 'name' => 'edit-button', 'title' => 'Редактировать']) ?>
                    <div>
                        <div class="edit-model-left">
                            <?php if(!empty($agency['address'])):?>
                                <div>
                                    <p>Загруженная шапка: </p>
                                    <a href="<?= '/' . $agency['address']?>" target="_blank"><?= $agency['address']?></a>
                                </div>
                            <?php endif;?>
                            <div class="upload-offer">
                                <?php $form = ActiveForm::begin([
                                    'options' => [
                                        'enctype' => 'multipart/form-data',
                                    ],
                                ]) ?>
                                <?= $form->field($uploadmodel, 'file')->fileInput(['accept' => 'application/pdf'])->label('Загрузить' . (!empty($agency['address']) ? ' новую' : '') . ' шапку (файл pdf): ') ?>
                                <?= $form->field($uploadmodel, 'hidden1')->hiddenInput(['value' => $agency['id']])->label(false); ?>
                                <button>Сохранить</button>
                                <?php ActiveForm::end() ?>
                            </div>
                        </div>
                        <div class="edit-model-left">
                            <?php if(!empty($agency['footer'])):?>
                                <div>
                                    <p>Загруженный футер: </p>
                                    <a href="<?= '/' . $agency['footer']?>" target="_blank"><?= $agency['footer']?>&nbsp;</a>
                                    <?= \yii\helpers\Html::submitButton('', ['class' => 'btn-del-footer btn btn-danger glyphicon glyphicon-remove', 'name' => 'del-button', 'title' => 'Удалить представительство']) ?>
                                </div>
                            <?php endif;?>
                            <div class="upload-offer">
                                <?php $form = ActiveForm::begin([
                                    'options' => [
                                        'enctype' => 'multipart/form-data',
                                    ],
                                ]) ?>
                                <?= $form->field($uploadmodel_footer, 'file')->fileInput(['accept' => 'application/pdf'])->label('Загрузить' . (!empty($agency['footer']) ? ' новый' : '') . ' футер (файл pdf): ') ?>
                                <?= $form->field($uploadmodel_footer, 'hidden1')->hiddenInput(['value' => $agency['id']])->label(false); ?>
                                <button>Сохранить</button>
                                <?php ActiveForm::end() ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach;?>
        <?php endif;?>
    </div>

    <hr>

    <div>
        <h4>Добавить новое представительство:</h4>
        <?php $form = ActiveForm::begin([
            'id' => 'addagency-form',
            'layout' => 'horizontal',
            'fieldConfig' => [
                'template' => "{label}{input}{error}",
                'labelOptions' => ['class' => 'col-5 control-label'],
            ],
            'options' => ['enctype' => 'multipart/form-data'],
        ]); ?>
        <?= $form->field($agencyform, 'name')->input('text')->hint('Поле не может быть пустым!'); ?>
        <?= $form->field($uploadmodel, 'file')->fileInput(['accept' => 'application/pdf'])->label('Загрузка шапки (файл pdf): ') ?>
        <?= $form->field($uploadmodel, 'hidden1')->hiddenInput(['value' => 'add-new'])->label(false); ?>

        <?= $form->field($uploadmodel_footer, 'file')->fileInput(['accept' => 'application/pdf'])->label('Загрузка файла с условиями оплаты и доставки (файл pdf): ') ?>
        <?= $form->field($uploadmodel_footer, 'hidden1')->hiddenInput(['value' => 'add-new'])->label(false); ?>
        <div class="row">
            <div class="col">
                <?= \yii\helpers\Html::submitButton('Добавить представительство', ['class' => 'btn btn-primary', 'name' => 'add-button', 'id' => 'btn-add-agency']) ?>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
    <div id="dialog" style="display:none" title="Удаление представительства"></div>
</div>

<?php
include 'settings-footer.php';
?>
