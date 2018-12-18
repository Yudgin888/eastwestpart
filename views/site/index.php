<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

include(Yii::getAlias('@app/functions.php'));

$this->title = 'My Yii Application';
?>
<div class="site-index">

    <div class="body-content">

        <?php
            $fileName = $sFilePath = mb_convert_encoding('D:\OpenServer\domains\eastwestpart\Price.xlsx', 'Windows-1251', 'UTF-8');
            $cats = loadPrice($fileName);


            echo '<pre>' . print_r($cats, true) . '</pre>';




        ?>


    </div>
</div>
