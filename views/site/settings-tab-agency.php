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
                            <p><?= (!empty($agency['name']) ? $agency['name'] : 'Название представительства не задано')?></p>
                            <?= \yii\helpers\Html::submitButton('', ['class' => 'btn-edit-agency btn btn-primary glyphicon glyphicon-pencil', 'name' => 'edit-button', 'title' => 'Редактировать']) ?>
                        </div>
                        <div class="tab2">
                            <input class='input-edit-name' type="text" value="<?= $agency['name']?>" >
                            <?= \yii\helpers\Html::submitButton('', ['class' => 'btn-save-agency btn btn-success glyphicon glyphicon-ok', 'name' => 'save-button', 'title' => 'Сохранить']) ?>
                            <?= \yii\helpers\Html::submitButton('', ['class' => 'btn-close-agency btn btn-danger glyphicon glyphicon-remove', 'name' => 'close-button', 'title' => 'Отмена']) ?>
                        </div>

                    </div>
                    <?= \yii\helpers\Html::submitButton('', ['class' => 'btn-del-agency btn btn-danger glyphicon glyphicon-remove', 'name' => 'del-button', 'title' => 'Удалить']) ?>
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
                                <?= $form->field($uploadmodel, 'file')->fileInput()->label('Загрузить' . (!empty($agency['address']) ? ' новую' : '') . ' шапку (файл pdf): ') ?>
                                <?= $form->field($uploadmodel, 'hidden1')->hiddenInput(['value' => $agency['id']])->label(false); ?>
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
        <?= $form->field($uploadmodel, 'file')->fileInput()->label('Загрузка шапки (файл pdf): ') ?>
        <?= $form->field($uploadmodel, 'hidden1')->hiddenInput(['value' => 'add-new'])->label(false); ?>
        <div class="row">
            <div class="col">
                <?= \yii\helpers\Html::submitButton('Добавить представительство', ['class' => 'btn btn-primary', 'name' => 'add-button', 'id' => 'btn-add-agency']) ?>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>

</div>

<?php
include 'settings-footer.php';
?>
