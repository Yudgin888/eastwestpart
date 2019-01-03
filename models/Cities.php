<?php

namespace app\models;

use yii\db\ActiveRecord;

class Cities extends ActiveRecord
{
//`id` int(11) NOT NULL AUTO_INCREMENT,
//`name` varchar(255) NOT NULL,

    public static function tableName()
    {
        return 'cr_of_cities';
    }

    public function __construct($name = '')
    {
        parent::__construct();
        $this->name = $name;
    }

    public function rules()
    {
        return [
            ['name', 'trim'],
        ];
    }
}