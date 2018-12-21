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
//`id` int(11) NOT NULL AUTO_INCREMENT,
//`name` varchar(255) NOT NULL,
//`parameters` longtext,
//`price` varchar(255),
//`id_category` int(11) NOT NULL,

    public static function tableName()
    {
        return 'model';
    }

    public function __construct($name = '', $parameters = '', $price = '', $id_category = 0){
        parent::__construct();
        $this->name = $name;
        $this->parameters = $parameters;
        $this->price = $price;
        $this->id_category = $id_category;
    }

    public function getOption(){
        return $this->hasMany(Option::class, ['id_model' => 'id']);
    }

    public function rules()
    {
        return [
            [['name', 'parameters'], 'trim'],
            [['price', 'id_category'], 'safe'],
        ];
    }
}