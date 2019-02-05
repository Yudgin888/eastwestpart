<?php
/**
 * Created by PhpStorm.
 * User: E.Pinchuk
 * Date: 21.12.2018
 * Time: 10:17
 */

namespace app\models;

use yii\base\Model;

class UserForm extends Model
{
    public $username;
    public $password;
    public $role;
    public $id_agency;

    public function attributeLabels()
    {
        return [
            'username' => 'Логин',
            'password' => 'Пароль',
            'role' => 'Группа',
            'id_agency' => 'Представительство',
        ];
    }

    public function rules()
    {
        return [
            [['username', 'password', 'role'], 'required', 'message' => 'Введите значение поля {attribute}'],
            [['username', 'password', 'id_agency'], 'trim'],
            ['username', 'validateUserName'],
            ['password', 'validatePassword'],
        ];
    }

    public function registration()
    {
        if ($this->validate()) {
            $model = new Users();
            $model->username = addslashes(htmlspecialchars($this->username));
            $model->password = \Yii::$app->getSecurity()->generatePasswordHash($this->password);
            $model->role = $this->role;
            $model->id_agency = $this->id_agency;
            $model->save();
            return true;
        }
        return false;
    }

    public function validateUserName($attribute, $params){
        if (!$this->hasErrors()) {
            $user = User::findByUsername($this->username);
            if ($user) {
                $this->addError($attribute, 'Указанное имя занято!');
            }
        }
    }

    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if (strlen($this->password) < 6) {
                $this->addError($attribute, 'Слишком короткий пароль!');
            }
        }
    }
}