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

        $data = \moonland\phpexcel\Excel::import($fileName, [
            'setFirstRecordAsKeys' => false, // if you want to set the keys of record column with first record, if it not set, the header with use the alphabet column on excel.
            'setIndexSheetByName' => true, // set this if your excel data with multiple worksheet, the index of array will be set with the sheet name. If this not set, the index will use numeric.
        ]);

        $cats = array_map(function($item) {
            return [
                    'name' => $item['A'],
                ];
        }, array_slice($data['Оглавление'], 1));

        $data = array_slice($data, 1);

        $i = 0;
        foreach($data as $datum){
            $cats[$i]['sheet'] = getSubCategories($datum);
            $i++;
        }
        ?>


<?php
        echo '<pre>' . print_r($cats, true) . '</pre>';
?>

    </div>
</div>
