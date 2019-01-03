<?php
/**
 * Created by PhpStorm.
 * User: E.Pinchuk
 * Date: 03.01.2019
 * Time: 9:37
 */

namespace app\models;


use yii\base\Model;
use yii\db\Exception;

class CityForm extends Model
{
    public $text;

    public function rules()
    {
        return [
            ['text', 'trim'],
        ];
    }

    public function save(){
        try {
            $arr = explode(',', $this->text);
            $arr = array_map('trim', $arr);
            $arr = array_unique($arr);
            Cities::deleteAll();
            foreach ($arr as $item) {
                if (!empty($item)) {
                    $city = new Cities($item);
                    $city->save();
                }
            }
        } catch (Exception $exception){
            return false;
        }
        return true;
    }
}