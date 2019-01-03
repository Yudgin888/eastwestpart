<?php
use yii\bootstrap\ActiveForm;
include 'settings-header.php';
?>

    <div class="setting-tab-item">
        <h3>Города/области:</h3>
        <?php $form = ActiveForm::begin([
            'id' => 'cities-form',
            'layout' => 'horizontal',
            'fieldConfig' => [
                'template' => "<div class=\"col-lg-12\">{input}</div><br><div class=\"col-lg-12\">{error}</div>",
            ],
        ]); ?>

        <?= $form->field($model, 'text')->textarea(['autofocus' => true, 'rows' => 10]) ?>

        <div class="form-group">
            <div class="col-lg-12">
                <?= \yii\helpers\Html::submitButton('Сохранить', ['class' => 'btn btn-primary', 'name' => 'save-button']) ?>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>

<?php
include 'settings-footer.php';
?>