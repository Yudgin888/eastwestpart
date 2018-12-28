<?php

namespace app\models;


use yii\db\ActiveRecord;

class Settings extends ActiveRecord
{
//`id` int(11) NOT NULL AUTO_INCREMENT,
//`name` varchar(255) NOT NULL,
//`value` longtext NOT NULL,

    public static function tableName()
    {
        return 'cr_of_settings';
    }

    public function __construct($name = '', $value = ''){
        parent::__construct();
        $this->name = $name;
        $this->value = $value;
    }

    public function rules()
    {
        return [
            [['name', 'value'], 'required'],
            [['name', 'value'], 'trim'],
        ];
    }
}