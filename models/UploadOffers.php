<?php
/**
 * Created by PhpStorm.
 * User: E.Pinchuk
 * Date: 22.12.2018
 * Time: 15:58
 */

namespace app\models;


use yii\base\Model;
use yii\db\Exception;
use yii\helpers\FileHelper;

//Класс множественной загрузки КМ для моделей

class UploadOffers extends Model
{
    public $files;

    public function rules()
    {
        return [
            [['files'], 'file', 'skipOnEmpty' => false, 'extensions' => 'pdf', 'maxFiles' => 0],
        ];
    }

    public function upload()
    {
        $result = [
            'success' => [],
            'error1' => [],
            'error2' => [],
        ];
        if ($this->validate()) {
            foreach ($this->files as $file) {
                try {
                    $path = \Yii::getAlias('@app/web/uploads/offers/' . $file->baseName);
                    if (!file_exists($path)) {
                        FileHelper::createDirectory($path);
                    }
                    $new_file_name = $file->baseName . '_' . time() . '.' . $file->extension;
                    $full_path = $path . '/' . $new_file_name;
                    $file->saveAs($full_path);
                    $path_db = 'uploads/offers/' . $file->baseName . '/' . $new_file_name;
                    ///!!!!!!!!!!!!!!!

                    $model = TModel::findOne(['name' => $file->baseName]);
                    if($model) {
                        $model->offer_path = $path_db;
                        $model->update();
                        $result['success'][] = $file->baseName . '.' . $file->extension;
                    } else {
                        $result['error2'][] = $file->baseName . '.' . $file->extension;
                    }
                }catch (Exception $e){
                    $result['error1'][] = $file->baseName . '.' . $file->extension;
                }
            }
        } else {
            $result['error1'][] = 'Файлы не были загружены!';
        }
        return $result;
    }
}