<?php
/**
 * Created by PhpStorm.
 * User: E.Pinchuk
 * Date: 22.12.2018
 * Time: 12:02
 */

namespace app\models;


use yii\base\Model;

class UploadFormKM extends Model
{
    public $file;
    public $hidden1;

    public function rules()
    {
        return [
            [['file'], 'file', 'skipOnEmpty' => false, 'extensions' => 'pdf', 'maxSize' => 10000000],
        ];
    }
}