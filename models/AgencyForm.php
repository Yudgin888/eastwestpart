<?php
/**
 * Created by PhpStorm.
 * User: E.Pinchuk
 * Date: 05.02.2019
 * Time: 9:45
 */

namespace app\models;


use yii\base\Model;

class AgencyForm extends Model
{
    public $name;
    public $address;

    public function attributeLabels()
    {
        return [
            'name' => 'Название',
            'address' => 'Адрес',
        ];
    }

    public function rules()
    {
        return [
            ['name', 'required', 'message' => 'Введите значение поля {attribute}'],
            ['name', 'trim'],
            ['name', 'validateName'],
        ];
    }


    public function validateName($attribute){
        $agency = Agency::findByName($attribute);
        if($agency){
            return false;
        } else return true;
    }

}