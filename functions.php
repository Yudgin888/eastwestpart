<?php

include(Yii::getAlias('@app/CreateTables.php'));

function chechDB(){
    try {
        $db = \Yii::$app->db;
        if(empty($db)){
            echo 'Error connection DB';
            die;
        }
        $createTables = new CreateTables();
        $createTables->up();
    } catch (Exception $e) {
        echo 'Error connection DB';
        die;
    }
}

function loadPrice($fileName){
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
    return $cats;
}


function getSubCategories($cat, $parent_cat = '\*', $level = 1, &$i = 0){
    $result = [];
    $num_cat = '';
    $pattern = '/' . $parent_cat . '[1-9]+[.](?![1-9])/';
    $curr_item = [];
    for (; $i < count($cat); $i++){
        if(!isset($cat[$i])){
            continue;
        }
        $str = implode($cat[$i]);
        if(empty($str)){
            continue;
        }

        if(!empty($str) && preg_match('/\*[1-9]./', $str)){
            $matches = [];
            preg_match_all('/[1-9]./', $str, $matches);
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
            $curr_item['name'] = substr( $str, strlen($num_cat));
            $curr_item['num'] = $num_cat;
        } elseif (preg_match ('/' . $parent_cat .'[1-9]+[.][1-9]+[.](?![1-9])/', $str, $matches)) {
            $curr_item['child_cats'] = getSubCategories($cat, '(\\' . $num_cat . ')', $level + 1, $i);
        } elseif (!empty($curr_item['name'])) {
            $table = array_filter($cat[$i], function($element) {
                return !empty($element);
            });
            if(count($table) > 1){
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