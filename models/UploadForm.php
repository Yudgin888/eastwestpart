<?php

namespace app\models;

use yii\base\Model;

class UploadForm extends Model
{
    public $file;
    public $hidden1;
    public $extensions;
    public $maxSize = 10000000;

    public function rules()
    {
        return [
            [['file'], 'file', 'skipOnEmpty' => false, 'extensions' => $this->extensions, 'maxSize' => $this->maxSize],
            [['hidden1', 'extensions', 'maxSize'], 'safe'],
        ];
    }

    public function __construct($extensions = '.')
    {
        $this->extensions = $extensions;
        parent::__construct();
    }
}