<?php

namespace app\models;

use yii\web\IdentityInterface;

class User extends \yii\base\BaseObject implements IdentityInterface
{
    public $id;
    public $username;
    public $password;
    public $auth_key;
    public $role;
    public $accessToken;

    public static function findIdentity($id)
    {
        $result = Users::find()->asArray()->where(['id' => $id])->limit(1)->all();
        if($result && $result[0]){
            $res = $result[0];
            $user = new User($res['id'], $res['username'], $res['password'], $res['auth_key'], $res['role']);
            return $user;
        }
        return null;
    }

    public static function findByUsername($username)
    {
        $result = Users::find()->asArray()->where(['username' => $username])->limit(1)->all();
        if($result && $result[0]){
            $res = $result[0];
            $user = new User($res['id'], $res['username'], $res['password'], $res['auth_key'], $res['role']);
            return $user;
        }
        return null;
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null;
    }

    function __construct($id, $username, $password, $auth_key, $role, $accessToken = NULL)
    {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->auth_key = $auth_key;
        $this->role = $role;
        $this->accessToken = $accessToken;
        parent::__construct();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getRole()
    {
        return $this->role;
    }

    public function getUserName()
    {
        return $this->username;
    }

    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey)
    {
        return $this->auth_key === $authKey;
    }

    public function validatePassword($password)
    {
        return $this->password === $password;
    }
}