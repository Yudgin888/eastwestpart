<?php
/**
 * Created by PhpStorm.
 * User: E.Pinchuk
 * Date: 21.12.2018
 * Time: 16:09
 */

namespace app\models;


use yii\db\ActiveRecord;

class Option extends ActiveRecord
{
//`id` int(11) NOT NULL AUTO_INCREMENT,
//`name` varchar(255) NOT NULL,
//`cost` varchar(255) NOT NULL,
//`basic` int(11) NOT NULL,
//`id_model` int(11) NOT NULL,

    public static function tableName()
    {
        return 'cr_of_option';
    }

    public function __construct($name = '', $cost = '', $basic = '', $id_model = ''){
        parent::__construct();
        $this->name = $name;
        $this->cost = $cost;
        $this->basic = $basic;
        $this->id_model = $id_model;
    }

    public function rules()
    {
        return [
            [['name', 'id_model'], 'required'],
            [['name', 'cost'], 'trim'],
            ['basic', 'safe'],
        ];
    }

    public function getModel()
    {
        return $this->hasOne(TModel::className(), ['id' => 'id_model']);
    }
}