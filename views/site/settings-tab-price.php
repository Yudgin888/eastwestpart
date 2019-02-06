<?php
    use yii\bootstrap\ActiveForm;
    include 'settings-header.php';
?>

<div class="setting-tab-item upload-price">
    <p>Категорий в базе данных: <?= $count_cat?></p>
    <p>Моделей в базе данных: <?= $count_mod?></p><br>
    <button class="btn btn-danger btn-model-cat-remove">Удалить все категории и модели из базы данных</button>
    <hr>
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