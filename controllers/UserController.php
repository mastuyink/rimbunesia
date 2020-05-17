<?php

namespace app\controllers;

use Yii;
use yii\filters\VerbFilter;
use app\models\UserRegisterLogin;


class UserController extends \yii\rest\Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
      //BYPASS RESPONSE & INPUT TO JSON
      Yii::$app->request->parsers = [
        'application/json' => 'yii\web\JsonParser',
      ];
      Yii::$app->response->format =  \yii\web\Response::FORMAT_JSON;
      return [
        'verbs' => [
          'class' => VerbFilter::className(),
          'actions' => [
            'register' => ['post'],
            'login'    => ['post'],
          ],
        ],
      ];
    }

    public function actionRegister()
    {
      $model = new UserRegisterLogin();
      if (!($response = $model->register(Yii::$app->request->post()))) {
        Yii::$app->response->statusCode = 400;
        Yii::$app->response->statusText = 'Bad Request (Validation Failed)';
        $response = $model->errorsCode;
      }
      return $response;
    }

    public function actionLogin()
    {
      $model = new UserRegisterLogin();
      if (!($response = $model->login(Yii::$app->request->post()))) {
        Yii::$app->response->statusCode = 400;
        Yii::$app->response->statusText = 'Bad Request (Validation Failed)';
        $response = $model->errorsCode;
      }else{
        if ($response == $model::RESPONSE_UNREGISTERED_USER) {
          Yii::$app->response->statusCode = 202;
          Yii::$app->response->statusText = 'Accepted (Unregistered User)';
          $response                       = Yii::$app->request->post();
        }
      }
      return $response;
    }

}
