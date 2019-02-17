<?php
/**
 * Created by PhpStorm.
 * User: yudgi
 * Date: 04.02.2019
 * Time: 22:42
 */

namespace app\models;

use yii\db\ActiveRecord;

class Agency extends ActiveRecord
{
//`id` int(11) NOT NULL AUTO_INCREMENT,
//`name` varchar(255) NOT NULL,
//`address` longtext,
//`footer` longtext,

    public static function tableName()
    {
        return 'cr_of_agency';
    }

    public function __construct($name = '', $address = '', $footer = ''){
        parent::__construct();
        $this->name = $name;
        $this->address = $address;
        $this->footer = $footer;
    }

    public function rules()
    {
        return [
            [['name', 'address', 'footer'], 'trim'],
        ];
    }

    public static function findByName($name)
    {
        $result = Agency::find()->asArray()->where(['name' => $name])->limit(1)->all();
        if($result && $result[0]){
            $res = $result[0];
            $agency = new Agency($res['name'], $res['address'], $res['footer']);
            $agency->id = $res['id'];
            return $agency;
        }
        return null;
    }
}