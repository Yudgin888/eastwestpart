<?php

namespace app\models;

use yii\db\ActiveRecord;

class Category extends ActiveRecord
{
    public static function tableName()
    {
        return 'category';
    }

    public function __construct($name, $num, $info, $id_par)
    {
        parent::__construct();
        $this->name = $name;
        $this->num = $num;
        $this->info = $info;
        $this->id_par = $id_par;
    }

    public function rules()
    {
        return [
            [['name', 'id_par'], 'required'],
            [['info', 'name', 'num'], 'trim'],
        ];
    }
}