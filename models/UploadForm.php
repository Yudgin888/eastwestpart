<?php
/**
 * Created by PhpStorm.
 * User: E.Pinchuk
 * Date: 19.12.2018
 * Time: 12:15
 */

namespace app\models;


class UploadForm extends Model
{
    public $file;

    public function rules()
    {
        return [
            [['file'], 'file', 'skipOnEmpty' => false, 'extensions' => 'xlsx', 'maxSize' => 10000000],
        ];
    }
}