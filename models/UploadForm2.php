<?php

namespace app\models;

use yii\base\Model;

class UploadForm2 extends Model
{
    public $file;
    public $hidden1;
    public $extensions;
    public $skipOnEmpty;
    public $maxSize = 10000000;

    public function rules()
    {
        return [
            [['file'], 'file', 'skipOnEmpty' => $this->skipOnEmpty, 'extensions' => $this->extensions, 'maxSize' => $this->maxSize],
            [['hidden1', 'extensions', 'maxSize'], 'safe'],
        ];
    }

    public function __construct($extensions = '.', $skipOnEmpty = false)
    {
        $this->extensions = $extensions;
        $this->skipOnEmpty = $skipOnEmpty;
        parent::__construct();
    }
}