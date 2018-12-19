<?php

include(Yii::getAlias('@app/CreateTables.php'));

function resetTable(){
    try {
        $db = \Yii::$app->getDb();
        if(empty($db)){
            echo 'Error connection DB';
            die;
        }
        CreateTables::reset();
    } catch (Exception $e) {
        echo $e->getMessage();
        die;
    }
}

function loadPrice($fileName){
    resetTable();
    $cats = parsePrice($fileName);
    foreach ($cats as $cat) {
        if(!empty($cat)) {
            if (!parseArr($cat[0])) {
                return false;
            }
        }
    }
    return true;
}

function parseArr($cat, $parent_id = 0){
    $name = trim($cat['name']);
    $info = isset($cat['info']) ? implode('\\n', $cat['info']) : '';
    $num = isset($cat['num']) ? str_replace('*', '', $cat['num']) : '';
    $category = new \app\models\Category($name, $num, $info, $parent_id);
    if($category->save()){
        $id = Yii::$app->db->lastInsertID;
        if(isset($cat['models']) && count($cat['models']) > 0) {
            foreach ($cat['models'] as $model){
                $name = $model['0'];
                $parameters = $model['1'];
                $price = $model['2'];
                $model = new \app\models\TModel($name, $parameters, $price, $id);
                if(!$model->save()) return false;
            }
        }
        if(isset($cat['child_cats']) && count($cat['child_cats']) > 0){
            foreach ($cat['child_cats'] as $child) {
                parseArr($child, $id);
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

        if(!empty($str) && preg_match('/\*[0-9]./', $str)){
            $matches = [];
            preg_match_all('/[0-9]./', $str, $matches);
            if(!empty($matches) && (count($matches[0]) < $level || count($matches[0]) > ($level + 1))){
                if(!empty($curr_item)) {
                    $result[] = $curr_item;
                }
                $i--;
                return $result;
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
                if(isset($curr_item['models'])){
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