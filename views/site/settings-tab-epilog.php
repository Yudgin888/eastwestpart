<?php
use yii\bootstrap\ActiveForm;
include 'settings-header.php';
?>

    <div class="setting-tab-item upload-epilog">
        <h4>Условия оплаты и доставки (указанный файл будет прикрепляться ко всем моделям, для которых не были указаны условия оплаты и доставки)</h4>
        <?php if(!empty($epilog) && !empty($epilog['value'])):?>
        <div>
            <p>Загруженный эпилог: </p>
            <a href="<?= '/' . $epilog['value']?>" target="_blank"><?= $epilog['value']?></a>
        </div>
        <?php endif;?>
        <?php $form = ActiveForm::begin([
            'options' => ['enctype' => 'multipart/form-data'],
        ]) ?>
        <?= $form->field($uploadmodel, 'file')->fileInput()->label('Загрузка эпилога (файл pdf): ') ?>
        <button>Отправить</button>
        <?php ActiveForm::end() ?>
    </div>

<?php
include 'settings-footer.php';
?>