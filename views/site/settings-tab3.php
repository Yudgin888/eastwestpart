<?php
use yii\bootstrap\ActiveForm;
include 'settings-header.php';
?>

    <div class="setting-tab-item upload-cost">
        <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>
        <?= $form->field($model, 'files[]')->fileInput(['multiple' => true, 'accept' => '*.xlsx'])->label('Загрузка опций (файлы xlsx): ') ?>
        <button>Отправить</button>
        <?php ActiveForm::end() ?>
    </div>



<?php
include 'settings-footer.php';
?>