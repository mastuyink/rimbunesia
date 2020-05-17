<?php
namespace app\models;

use Yii;

class UserRegisterLogin extends \yii\base\Model
{
    public $phone_number;
    public $full_name;
    public $email;
    public $age;
    public $gender;
    public $password;
    public $password_confirm;
    public $social_id;
    public $social_media;

    private $_userModel;

    const SCENARIO_REGISTER_FORM     = 'registerFormScenario';
    const SCENARIO_REGISTER_SOCIAL   = 'registerSocialScenario';
    const SCENARIO_LOGIN_FORM        = 'loginScenario';
    const SCENARIO_LOGIN_SOCIAL      = 'loginSocialScenario';

    const RESPONSE_UNREGISTERED_USER = 202;

    const ERROR_CODE_REQUIRED       = 'REQUIRED';
    const ERROR_CODE_INVALID_FORMAT = 'INVALID_FORMAT';
    const ERROR_CODE_INVALID_VALUE  = 'INVALID_VALUE';
    const ERROR_CODE_DUPLICATED     = 'DUPLICATED';
    const ERROR_CODE_TO_SHORT       = 'TO_SMALL';
    const ERROR_CODE_TO_LONG        = 'TO_BIG';
    const ERROR_CODE_DISABLE        = 'DISABLE';

    public function rules()
    {
      return [
        [['phone_number','full_name','email','age','gender','password','password_confirm','social_media','social_id'],'required','message'=> self::ERROR_CODE_REQUIRED],
        [['email'], 'email','message'=> self::ERROR_CODE_INVALID_FORMAT],
        [['gender'],'in','range'=>UserIdentity::getGenders(),'message'=> self::ERROR_CODE_INVALID_VALUE],
        [['social_media'],'in','range'=>UserIdentity::getSocials(),'message'=> self::ERROR_CODE_INVALID_VALUE],
        [['password_confirm'], 'compare', 'compareAttribute' => 'password','operator'=>'==','message'=> self::ERROR_CODE_INVALID_VALUE],
        [['age'],'number','min'=> 1,'max' => 125,'message'=>self::ERROR_CODE_INVALID_VALUE,'tooSmall'=> self::ERROR_CODE_TO_SHORT,'tooBig'=>self::ERROR_CODE_TO_LONG],
        [['email','full_name'], 'string','min'=>3, 'max' => 100,'tooShort'=> self::ERROR_CODE_TO_SHORT,'tooLong'=>self::ERROR_CODE_TO_LONG],
        [['password'], 'string', 'min' => 6,'max' => 18,'tooShort'=> self::ERROR_CODE_TO_SHORT,'tooLong'=>self::ERROR_CODE_TO_LONG],
        [['phone_number'],'string','min'=>6,'max'=>25,'tooShort'=> self::ERROR_CODE_TO_SHORT,'tooLong'=>self::ERROR_CODE_TO_LONG],
        [['phone_number'],function ($attribute, $params) {
          if (preg_match('/\d/', $this->$attribute)) {
            if(preg_match('/\D/', $this->$attribute)){
              $this->addError($attribute,$this::ERROR_CODE_INVALID_VALUE);
              $response = false;
            }else{
              $response = true;
            }
          }else{
            if (preg_match('/[^٠-٩]/', $this->$attribute)){
              $this->addError($attribute,$this::ERROR_CODE_INVALID_VALUE);
              $response = false;
            }else{
              $response = true;
            }
          }
          return $response;
        }],
        [['email','phone_number'],'unique','targetClass' => '\app\models\UserIdentity','when'=>function($model){
          return ($model->scenario == $model::SCENARIO_REGISTER_FORM || $model->scenario == $model::SCENARIO_REGISTER_SOCIAL);
        },'message'=> self::ERROR_CODE_DUPLICATED],
        [['social_id'],function($attribute,$params){
          $findDuplicate = UserIdentity::find()->where(['social_id'=>$this->$attribute])->andWhere(['social_media'=>$this->social_media])->asArray()->one();
          if ($findDuplicate != NULL) {
            $this->addError($attribute,$this::ERROR_CODE_DUPLICATED);
            return false;
          }
          return true;
        },'when'=>function($model){
          return $model->scenario == $model::SCENARIO_REGISTER_SOCIAL;
        }],
      ];
    }

    public function scenarios(){
    	$scenario 																 = parent::scenarios();
			$scenario[$this::SCENARIO_REGISTER_FORM] 	 = ['phone_number','password','password_confirm','full_name','email','age','gender'];
			$scenario[$this::SCENARIO_REGISTER_SOCIAL] = ['phone_number','full_name','email','age','gender','social_media','social_id'];
      $scenario[$this::SCENARIO_LOGIN_SOCIAL]    = ['email','social_media','social_id'];
			$scenario[$this::SCENARIO_LOGIN_FORM] 		 = ['phone_number','password'];
    	return $scenario;
    }

    public function login($data){
      if (isset($data['social_media']) || isset($data['social_id']) || isset($data['email'])) {
        $this->scenario = $this::SCENARIO_LOGIN_SOCIAL;
        $loginFunction = 'loginViaSocial';
      }else{
        $this->scenario = $this::SCENARIO_LOGIN_FORM;
        $loginFunction = 'loginViaForm';
      }
      $this->load([$this->formName() => $data]);
      if (!$this->validate()) {
        return false;
      }
      return $this->$loginFunction();
    }

    public function register($data)
    {
    	if (isset($data['social_media']) && $data['social_media'] != NULL && isset($data['social_id']) && $data['social_id'] != NULL) {
    		$this->scenario = $this::SCENARIO_REGISTER_SOCIAL;
        $this->password = Yii::$app->security->generateRandomString(25);
    	}else{
    		$this->scenario = $this::SCENARIO_REGISTER_FORM;
    	}
      $this->load([$this->formName() => $data]);
      if (!$this->validate()) {
        return false;
      }
    	return $this->registerUser();
    }

    protected function loginViaForm(){
      if (($modelUser = UserIdentity::findIdentityByPhone($this->phone_number)) && $modelUser->validatePassword($this->password)) {
        $response = $modelUser->login();
      }else{
        $this->addErrors(['phone_number'=>self::ERROR_CODE_INVALID_VALUE,'password'=>self::ERROR_CODE_INVALID_VALUE]);
        $response = false;
      }
      return $response;
    }

    protected function loginViaSocial(){
      $findRegisteredUser = UserIdentity::find()->where(['social_id'=>$this->social_id])->andWhere(['social_media' => $this->social_media])->andWhere(['email' => $this->email])->one();
      if ($findRegisteredUser != NULL) {
        if ($findRegisteredUser->status == $findRegisteredUser::STATUS_ENABLE) {
          return $findRegisteredUser->login();
        }else{
          $this->addError('user',self::ERROR_CODE_DISABLE);
          return false;
        }
      }else{
        return $this::RESPONSE_UNREGISTERED_USER;
      }
    }

    protected function registerUser(){
			$saveUser               = new UserIdentity();
			$saveUser->full_name    = $this->full_name;
			$saveUser->email        = $this->email;
			$saveUser->phone_number = $this->phone_number;
			$saveUser->age          = $this->age;
			$saveUser->gender       = $this->gender;
			$saveUser->social_media = $this->social_media;
			$saveUser->social_id    = $this->social_id;
			$saveUser->setPassword($this->password);
			$saveUser->setAuthKey();
			$saveUser->save(false);
			$response = $this->attributes;
    	unset($response['password'],$response['password_confirm']);
    	return $response;
    }

    public function getErrorsCode(){
      foreach ($this->errors as $attribute => $errors) {
        $errorsCode[$attribute] = reset($errors);
      }
      return isset($errorsCode) ? $errorsCode : [];
    }
}