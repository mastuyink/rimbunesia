<?php

namespace app\models;
use Yii;

class UserIdentity extends TUser implements \yii\web\IdentityInterface
{
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ENABLE]);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return false;
    }

    public static function findByUsername($username)
    {
        return false;
    }

    public static function findIdentityByPhone($phone){
        return static::findOne(['phone_number' => $phone, 'status' => self::STATUS_ENABLE]);
    }

    public function getId()
    {
        return $this->id;
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
        return Yii::$app->security->validatePassword($password, $this->password_encrypt);
    }

    public function setPassword($password)
    {
        $this->password_encrypt = Yii::$app->security->generatePasswordHash($password);
    }

    public function setAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString(50);
    }

    public function login(){
        $user =  Yii::$app->user;
        $user->login($this);
        if ($this->auth_key == NULL) {
            $this->setAuthKey();
        }
        $this->last_login = date('Y-m-d H:i:d');
        $this->save(false);
        return [
          'access_token'=> $user->identity->auth_key,
        ];
    }
}
