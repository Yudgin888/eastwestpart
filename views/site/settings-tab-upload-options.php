<?php
use yii\bootstrap\ActiveForm;
include 'settings-header.php';
?>
    <div class="setting-tab-item upload-cost">
        <p>Всего опций: <?= $count_opt?></p>
        <p>Моделей в базе: <?= $count_mod?></p>
        <?php if($count_mod == 0):?>
            <p>Перед загрузкой опций для начала загрузите список категорий и моделей (опция привязывается к определенной модели)</p>
            <a href="/settings?tab=upload-price">Загрузка прайса</a>
        <?php else:?>
            <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>
            <?= $form->field($model, 'files[]')->fileInput(['multiple' => true, 'accept' => '*.xlsx'])->label('Загрузка опций (файлы xlsx): ') ?>
            <button>Отправить</button>
            <?php ActiveForm::end() ?>
        <?php endif;?>
    </div>

<?php
include 'settings-footer.php';
?>