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