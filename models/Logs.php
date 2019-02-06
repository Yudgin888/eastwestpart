<?php
/**
 * Created by PhpStorm.
 * User: E.Pinchuk
 * Date: 06.02.2019
 * Time: 13:29
 */

namespace app\models;


use yii\db\ActiveRecord;

class Logs extends ActiveRecord
{
//`id` int(11) NOT NULL AUTO_INCREMENT,
//`date` varchar(255) NOT NULL,
//`message` longtext,
//`status` int(11) DEFAULT 0,

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'date' => 'Дата',
            'message' => 'Сообщение',
            'status' => 'Статус',
        ];
    }

    public static function tableName()
    {
        return 'cr_of_logs';
    }

    public function __construct($date = '', $message = '', $status = 0){
        parent::__construct();
        $this->date = $date;
        $this->message = $message;
        $this->status = $status;
    }

//    public function rules()
//    {
//        return [
//            [['id', 'date', 'message'], 'trim'],
//            ['status', 'safe'],
//        ];
//    }

    public static function findById($id)
    {
        $result = Logs::find()->asArray()->where(['id' => $id])->limit(1)->all();
        if($result && $result[0]){
            $res = $result[0];
            $logs = new Logs($res['date'], $res['message'], $res['status']);
            $logs->id = $res['id'];
            return $logs;
        }
        return null;
    }

    public static function addLog($message, $status = 0)
    {
        try {
            $logs = new Logs(date('h:i:s d.m.Y', time()), $message, $status);
            $logs->save();
        } catch (\Throwable $ex){}
    }
}