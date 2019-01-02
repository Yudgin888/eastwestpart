<?php

namespace app\models;

use yii\db\ActiveRecord;

class Category extends ActiveRecord
{
//`id` int(11) NOT NULL AUTO_INCREMENT,
//`name` varchar(255) NOT NULL,
//`num` varchar(255) NOT NULL,
//`info` longtext NOT NULL,
//`id_par` int(11) NOT NULL DEFAULT 0,
//`ism` int(11) NOT NULL DEFAULT 0,

    public static function tableName()
    {
        return 'cr_of_category';
    }

    public function __construct($name, $num, $info, $id_par, $ism)
    {
        parent::__construct();
        $this->name = $name;
        $this->num = $num;
        $this->info = $info;
        $this->id_par = $id_par;
        $this->ism = $ism;
    }

    public function rules()
    {
        return [
            [['name', 'id_par'], 'required'],
            [['info', 'name'], 'trim'],
            [['ism', 'num'], 'safe'],
        ];
    }
}