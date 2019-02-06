<?php

use app\myClass\CreateTables;

function parseCostFile($fileName){
    $option_count = 0;
    try {
        $data = \moonland\phpexcel\Excel::import($fileName, [
            'setFirstRecordAsKeys' => false, // if you want to set the keys of record column with first record, if it not set, the header with use the alphabet column on excel.
            'setIndexSheetByName' => true, // set this if your excel data with multiple worksheet, the index of array will be set with the sheet name. If this not set, the index will use numeric.
        ]);
        foreach ($data as $key => $value) {
            $model_id = \app\models\TModel::find()->asArray()->where(["name" => $key])->all()[0]['id'];
            if(\app\models\Option::deleteAll(['id_model' => $model_id])){
                $option_count -= 1;
            }
            if (!empty($model_id)) {
                $basic = 1;
                for ($i = 3; $i < count($value) + 1; $i++) {
                    $name = $value[$i]['A'];
                    if (!empty($name)) {
                        if (strpos($name, 'опции') !== false || strpos($name, 'Опции') !== false) {
                            $basic = 0;
                            continue;
                        }
                        $cost = $value[$i]['C'];
                        $option = new \app\models\Option($name, $cost, $basic, $model_id);
                        $option->save();
                        $option_count++;
                    }
                }
            }
        }
    } catch (Exception $ex){
        return [
            'code' => 'error',
            'mess' => $ex->getMessage(),
        ];
    }
    \app\models\Logs::addLog(Yii::$app->user->identity->username . " обновил список опций (добавлено: {$option_count}", 2);
    return [
        'code' => 'success',
        'mess' => $option_count,
    ];
}


function loadPrice($fileName){
    //resetTable();
    $cats = parsePrice($fileName);
    $cats_count = 0;
    $mdl_count = 0;
    foreach ($cats as $cat) {
        if(!empty($cat)) {
            if (!parseArr($cat[0], 0, $cats_count, $mdl_count)) {
                return false;
            }
        }
    }
    \app\models\Logs::addLog(Yii::$app->user->identity->username . " обновил список моделей и категорий (моделей: {$mdl_count}, категорий: {$cats_count}", 2);
    return [
        'cats_count' => $cats_count,
        'mdl_count' => $mdl_count,
    ];
}

function parseArr($cat, $parent_id = 0, &$cats_count, &$mdl_count){
    $name = trim($cat['name']);
    $info = isset($cat['info']) ? implode('\\n', $cat['info']) : '';
    $num = isset($cat['num']) ? str_replace('*', '', $cat['num']) : '';
    $ism = empty($cat['models']) ? 0 : 1;
    $category = new \app\models\Category($name, $num, $info, $parent_id, $ism);
    if($category->save()){
        $cats_count++;
        $id = Yii::$app->db->lastInsertID;
        if(isset($cat['models']) && count($cat['models']) > 0) {
            foreach ($cat['models'] as $model){
                $name = $model['0'];
                $parameters = $model['1'];
                $price = $model['2'];
                $model = new \app\models\TModel($name, $parameters, $price, $id);
                if(!$model->save()) return false;
                $mdl_count++;
            }
        }
        if(isset($cat['child_cats']) && count($cat['child_cats']) > 0){
            foreach ($cat['child_cats'] as $child) {
                parseArr($child, $id, $cats_count, $mdl_count);
            }
        }
        return true;
    } else return false;
}

function parsePrice($fileName){
    $data = \moonland\phpexcel\Excel::import($fileName, [
        'setFirstRecordAsKeys' => false, // if you want to set the keys of record column with first record, if it not set, the header with use the alphabet column on excel.
        'setIndexSheetByName' => true, // set this if your excel data with multiple worksheet, the index of array will be set with the sheet name. If this not set, the index will use numeric.
    ]);
    $data = array_slice($data, 1);
    $cats = [];
    foreach($data as $datum){
        $cats[] = getSubCategories($datum);
    }
    return $cats;
}

function getSubCategories($cat, $parent_cat = '\*', $level = 1, &$i = 0){
    $result = [];
    $num_cat = '';
    $pattern = '/' . $parent_cat . '[0-9]+[.](?![0-9])/';
    $curr_item = [];
    for (; $i < count($cat) + 1; $i++){
        if(!isset($cat[$i])){
            continue;
        }
        $str = implode($cat[$i]);
        if(empty($str)){
            continue;
        }

        if(!empty($str) && preg_match('/\*[0-9][.]/', $str)){
            $matches = [];
            preg_match('/[*][0-9.]{1,}/', $str, $number);
            if(!empty($number)) {
                preg_match_all('/[0-9]{1,}[.]{1}/', $number['0'], $matches);
                if (!empty($matches) && (count($matches[0]) < $level || count($matches[0]) > ($level + 1))) {
                    if (!empty($curr_item)) {
                        $result[] = $curr_item;
                    }
                    $i--;
                    return $result;
                }
            }
        }

        $matches = [];
        if(preg_match($pattern, $str, $matches)){
            if(!empty($curr_item)){
                $result[] = $curr_item;
                $curr_item = [];
            }
            $num_cat = $matches[0];
            $name = substr( $str, strlen($num_cat));
            //$name = preg_replace("/^(\\s)+/", "", $name);
            //$name = preg_replace("/^(\\s)*|(\\s)*$/", "", $name);
            $name = htmlentities($name);
            $name = str_replace("&nbsp;",'',$name);
            $curr_item['name'] = trim($name);
            $curr_item['num'] = $num_cat;
        } elseif (preg_match ('/' . $parent_cat .'[0-9]+[.][0-9]+[.](?![0-9])/', $str, $matches)) {
            $curr_item['child_cats'] = getSubCategories($cat, '(\\' . $num_cat . ')', $level + 1, $i);
        } elseif (!empty($curr_item['name'])) {
            $table = array_filter($cat[$i], function($element) {
                return !empty($element);
            });
            if(count($table) > 1){
                $table = array_values($table);
                if(isset($curr_item['models']) && $table['0'] != 'Модель'){
                    $curr_item['models'][] = $table;
                } else {
                    $curr_item['models'] = [];
                }
            } else {
                $curr_item['info'][] = $str;
            }
        }
    }
    if(!empty($curr_item)) {
        $result[] = $curr_item;
    }
    $i--;
    return $result;
}

function CreateTree($cats, $sub = 0)
{
    $res = array();
    foreach($cats as $cat)
    {
        if($sub == $cat['id_par'])
        {
            $b = CreateTree($cats, $cat['id']);
            if(!empty($b)) {
                $cat['childs'] = $b;
            }
            $res[] = $cat;
        }
    }
    return $res;
}

/**
 * Функция возвращает окончание для множественного числа слова на основании числа и массива окончаний
 * param  $number Integer Число на основе которого нужно сформировать окончание
 * param  $endingsArray  Array Массив слов или окончаний для чисел (1, 4, 5),
 *         например array('яблоко', 'яблока', 'яблок')
 * return String
 */
function getNumEnding($number, $endingArray)
{
    $number = $number % 100;
    if ($number >= 11 && $number <= 19) {
        $ending = $endingArray[2];
    }
    else {
        $i = $number % 10;
        switch ($i)
        {
            case (1): $ending = $endingArray[0]; break;
            case (2):
            case (3):
            case (4): $ending = $endingArray[1]; break;
            default: $ending = $endingArray[2];
        }
    }
    return $ending;
}