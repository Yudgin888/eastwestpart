<?php
/**
 * Created by PhpStorm.
 * User: E.Pinchuk
 * Date: 19.12.2018
 * Time: 12:49
 */

namespace app\models;

use yii\db\ActiveRecord;

class TModel extends ActiveRecord
{
//    public $name;
//    public $parameters;
//    public $price;
//    public $id_category;

    public static function tableName()
    {
        return 'model';
    }

    public function __construct($name, $parameters, $price, $id_category){
        parent::__construct();
        $this->name = $name;
        $this->parameters = $parameters;
        $this->price = $price;
        $this->id_category = $id_category;
    }

    public function rules()
    {
        return [
            [['name', 'id_category'], 'required'],
            [['parameters', 'price'], 'trim'],
        ];
    }
}