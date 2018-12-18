<?php
/**
 * Created by PhpStorm.
 * User: E.Pinchuk
 * Date: 18.12.2018
 * Time: 16:58
 */

namespace app\models;


use yii\db\ActiveRecord;

class Model extends ActiveRecord
{
    public static function tableName()
    {
        return 'model';
    }
}