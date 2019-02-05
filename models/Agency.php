<?php
/**
 * Created by PhpStorm.
 * User: yudgi
 * Date: 04.02.2019
 * Time: 22:42
 */

namespace app\models;


use yii\base\Model;

class Agency extends Model
{
//`id` int(11) NOT NULL AUTO_INCREMENT,
//`name` varchar(255) NOT NULL,
//`address` longtext,

    public static function tableName()
    {
        return 'cr_of_agency';
    }

    public function __construct($name = '', $address = ''){
        parent::__construct();
        $this->name = $name;
        $this->address = $address;
    }

    public function rules()
    {
        return [
            [['name', 'address'], 'trim'],
        ];
    }
}