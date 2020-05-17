<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "t_user".
 *
 * @property int $id
 * @property string $full_name
 * @property string $email
 * @property string $phone_number
 * @property string $password_encrypt
 * @property string|null $access_token
 * @property string $gender
 * @property int $age
 * @property string|null $social_id
 * @property string|null $social_media
 * @property string $status
 * @property string $last_login
 * @property string $create_at
 * @property string $update_at
 */
class TUser extends \yii\db\ActiveRecord
{

    const STATUS_ENABLE  = 'E';
    const STATUS_DISABLE = 'D';

    const GENDER_MALE    = 'M';
    const GENDER_FEMALE  = 'F';

    const SOCIAL_FACEBOOK = 'FACEBOOK';
    const SOCIAL_GOOGLE   = 'GOOGLE';

    public static function tableName()
    {
        return 't_user';
    }

    public static function getGenders(){
        return [
            static::GENDER_MALE,
            static::GENDER_FEMALE
        ];
    }

    public static function getSocials(){
        return [
            static::SOCIAL_FACEBOOK,
            static::SOCIAL_GOOGLE,
        ];
    }

    public function beforeSave($insert){
        if (!parent::beforeSave($insert)) {
            return false;
        }
        $now = date('Y-m-d H:i:s');
        if ($this->isNewRecord) {
            $this->create_at  = $now;
            $this->last_login = NULL;
            $this->status     = $this::STATUS_ENABLE;
        }
        $this->update_at = $now;
        return true;
    }
}
