<?php
    use yii\bootstrap\ActiveForm;
    include 'settings-header.php';
?>

<div class="setting-tab-item upload-price">
    <?php $form = ActiveForm::begin([
            'options' => ['enctype' => 'multipart/form-data'],
    ]) ?>
    <?= $form->field($uploadmodel, 'file')->fileInput()->label('Загрузка прайса (файлы xlsx): ') ?>
    <button>Отправить</button>
    <?php ActiveForm::end() ?>
</div>

<?php
    include 'settings-footer.php';
?>