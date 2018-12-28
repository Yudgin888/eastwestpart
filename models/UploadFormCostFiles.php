<?php
/**
 * Created by PhpStorm.
 * User: E.Pinchuk
 * Date: 21.12.2018
 * Time: 14:27
 */

namespace app\models;

use yii\base\Model;
use yii\db\Exception;
use yii\helpers\FileHelper;

//Класс загрузки опций

class UploadFormCostFiles extends Model
{
    public $files;

    public function rules()
    {
        return [
            [['files'], 'file', 'skipOnEmpty' => false, 'extensions' => 'xlsx', 'maxFiles' => 0],
        ];
    }

    public function upload()
    {
        $result['code'] = 'success';
        if ($this->validate()) {
            $path = \Yii::getAlias('@app/web/uploads/costfiles');
            FileHelper::removeDirectory($path);
            FileHelper::createDirectory($path);
            foreach ($this->files as $file) {
                try {
                    $full_path = $path . '/' . $file->baseName . '_' . time() . '.' . $file->extension;
                    $file->saveAs($full_path);
                    $result['files'][] = [
                        'path' => $full_path,
                        'file_name' => $file->baseName . '.' . $file->extension,
                    ];
                }catch (Exception $e){
                    $result['code'] = 'error';
                    $result['mess'] = 'Не удалось загрузить файлы!';
                    $result['files'][] = $file->baseName . '.' . $file->extension;
                }
            }
        } else {
            $result['code'] = 'error';
            $result['mess'] = 'Не удалось загрузить файлы!';
            $result['files'] = [];
        }
        return $result;
    }
}