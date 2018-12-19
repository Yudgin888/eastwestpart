<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'My Yii Application';
?>
<div class="site-index">

    <?php
    if(Yii::$app->session->hasFlash('success-load')):?>
        <div class="alert alert-success alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <strong>Warning!</strong><?= Yii::$app->session->getFlash('success-load');?>
        </div>
    <?php endif;?>
    <?php
    if(Yii::$app->session->hasFlash('error-load')):?>
        <div class="alert alert-danger alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <strong>Warning!</strong><?= Yii::$app->session->getFlash('error-load');?>
        </div>
    <?php endif;?>
    <?php
    if(Yii::$app->session->hasFlash('success-proc')):?>
        <div class="alert alert-success alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <strong>Warning!</strong><?= Yii::$app->session->getFlash('success-proc');?>
        </div>
    <?php endif;?>
    <?php
    if(Yii::$app->session->hasFlash('error-proc')):?>
        <div class="alert alert-danger alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <strong>Warning!</strong><?= Yii::$app->session->getFlash('error-proc');?>
        </div>
    <?php endif;?>


    <div class="body-content">

        <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>
        <?= $form->field($uploadmodel, 'file')->fileInput() ?>
        <button>Отправить</button>
        <?php ActiveForm::end() ?>

    </div>
</div>
