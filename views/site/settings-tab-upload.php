<?php

use yii\bootstrap\ActiveForm;

include 'settings-header.php';
?>

    <div class="setting-tab-item upload-block">
        <p>Категорий: <?= $count_cat ?></p>
        <p>Моделей: <?= $count_mod ?></p>
        <p>Опций: <?= $count_opt ?></p><br>
        <button class="btn btn-danger btn-model-cat-remove">Удалить все категории и модели из базы данных</button>
        <hr>
        <?php $form = ActiveForm::begin([
            'options' => ['enctype' => 'multipart/form-data'],
        ]) ?>
        <?= $form->field($uploadmodel, 'file')->fileInput()->label('Загрузка прайса (файлы xlsx): ') ?>
        <button>Отправить</button>
        <?php ActiveForm::end() ?>
    </div>

    <div class="setting-tab-item upload-block">
        <button class="btn btn-danger btn-option-remove">Удалить все опции из базы данных</button>
        <hr>
        <?php if ($count_mod == 0): ?>
            <p>Перед загрузкой опций для начала загрузите список категорий и моделей (опция привязывается к определенной
                модели)</p>
        <?php else: ?>
            <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>
            <?= $form->field($model, 'files[]')->fileInput(['multiple' => true, 'accept' => '*.xlsx'])->label('Загрузка опций (файлы xlsx): ') ?>
            <button>Отправить</button>
            <?php ActiveForm::end() ?>
        <?php endif; ?>
    </div>

    <div class="setting-tab-item upload-epilog upload-block">
        <h4>Условия оплаты и доставки (указанный файл будет прикрепляться ко всем моделям, для которых не были указаны условия оплаты и доставки)</h4>
        <?php if(!empty($epilog) && !empty($epilog['value'])):?>
            <div>
                <p>Загруженный футер: </p>
                <a href="<?= '/' . $epilog->value?>" target="_blank"><?= $epilog->value?></a>
            </div>
        <?php endif;?>
        <?php $form = ActiveForm::begin([
            'options' => ['enctype' => 'multipart/form-data'],
        ]) ?>
        <?= $form->field($uploadmodel_epilog, 'file')->fileInput()->label('Загрузка эпилога (файл pdf): ') ?>
        <button>Отправить</button>
        <?php ActiveForm::end() ?>
    </div>

    <div id="dialog" style="display:none" title="Удаление"></div>

<?php
include 'settings-footer.php';
?>