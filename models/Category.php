<?php

namespace app\models;

use yii\db\ActiveRecord;

class Category extends ActiveRecord
{
    //public $name;
    //public $info;
    //public $id_par;

    public static function tableName()
    {
        return 'category';
    }

    public function __construct($name, $info, $id_par)
    {
        parent::__construct();
        $this->name = $name;
        $this->info = $info;
        $this->id_par = $id_par;
    }

    public function rules()
    {
        return [
            [['name', 'id_par'], 'required'],
            [['info', 'name'], 'trim'],
        ];
    }
}