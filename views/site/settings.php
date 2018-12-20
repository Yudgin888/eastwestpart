<?php
use yii\widgets\ActiveForm;

$this->title = 'Создание КП | Настройки';
?>
<div class="site-index">
    <?php
    if(Yii::$app->session->hasFlash('success-load')):?>
        <div class="alert alert-success alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <?= Yii::$app->session->getFlash('success-load');?>
        </div>
    <?php endif;?>
    <?php
    if(Yii::$app->session->hasFlash('error-load')):?>
        <div class="alert alert-danger alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <?= Yii::$app->session->getFlash('error-load');?>
        </div>
    <?php endif;?>
    <?php
    if(Yii::$app->session->hasFlash('success-proc')):?>
        <div class="alert alert-success alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <?= Yii::$app->session->getFlash('success-proc');?>
        </div>
    <?php endif;?>
    <?php
    if(Yii::$app->session->hasFlash('error-proc')):?>
        <div class="alert alert-danger alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <?= Yii::$app->session->getFlash('error-proc');?>
        </div>
    <?php endif;?>
    <div class="body-content">
        <h1>Настройки</h1>

        <div class="upload-price">
            <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>
            <?= $form->field($uploadmodel, 'file')->fileInput()->label('Загрузка прайса (exel-файл): ') ?>
            <button>Отправить</button>
            <?php ActiveForm::end() ?>
        </div>
        <hr>

        <div class="users">
            <h2>Пользователи:</h2>
            <div class="container">
                <?php if($users && count($users) > 0):?>
                    <table class="table">
                        <thead class="thead-light">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Логин</th>
                                <th scope="col">Пароль</th>
                                <th scope="col">Handle</th>
                            </tr>
                        </thead>
                        <tbody>
                    <?php $i = 1; foreach ($users as $item): ?>
                            <tr>
                                <th scope="row"><?= $i?></th>
                                <td>Mark</td>
                                <td>Otto</td>
                                <td>@mdo</td>
                            </tr>
                    <?php $i++; endforeach;?>
                        </tbody>
                    </table>
                <?php endif;?>

            </div>
            <br>
            <?php if(!Yii::$app->user->isGuest && $modelReview != NULL):?>
                <div class="container">
                    <h3>Добавить отзыв</h3>
                    <?php $form = ActiveForm::begin([
                        'id' => 'review-form',
                        'layout' => 'horizontal',
                        'fieldConfig' => [
                            'template' => "<div class=\"col\">{label}</div>\n<div class=\"col\">{input}</div>\n<div class=\"col\">{error}</div>",
                            'labelOptions' => ['class' => 'col-5 control-label'],
                        ],
                        'options' => ['enctype' => 'multipart/form-data'],
                    ]); ?>
                    <?= $form->field($modelReview, 'comment_text')->textarea(['rows' => 3, 'cols' => 8]); ?>
                    <?= $form->field($modelReview, 'rating')
                        ->radioList([
                            '1' => '1',
                            '2' => '2',
                            '3' => '3',
                            '4' => '4',
                            '5' => '5'
                        ],
                            [
                                'item' => function ($index, $label, $name, $checked, $value) {
                                    $check = $checked ? ' checked="checked"' : '';
                                    return "<label class=\"form__param\"><input type=\"radio\" name=\"$name\" value=\"$value\"$check> <i></i> $label</label>";
                                }]);?>
                    <?= $form->field($modelReview, 'imageFile')->fileInput(); ?>
                    <div class="row">
                        <div class="col">
                            <?= Html::submitButton('Добавить отзыв', ['class' => 'btn btn-primary', 'name' => 'review-button']) ?>
                        </div>
                    </div>
                    <?php ActiveForm::end(); ?>
                </div>
            <?php endif;?>
        </div>

    </div>
</div>