<?php

namespace app\controllers;

use Yii;
use app\models\TUser;
use app\models\UserSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * BackendController implements the CRUD actions for TUser model.
 */
class BackendController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    //'change-status' => ['POST'],
                ],
            ],
        ];
    }
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionChangeStatus($id)
    {
      $model = $this->findModel($id);
      $data = Yii::$app->request->post('status',$model::STATUS_DISABLE);
      if (in_array($data,[$model::STATUS_ENABLE,$model::STATUS_DISABLE])) {
      	$model->status = $data;
      	$model->save(false);
      	Yii::$app->session->setFlash('success', 'Change Status Success');
      }else{
      	Yii::$app->session->setFlash('success', 'Status Invalid');
      }
      return true;
    }

    protected function findModel($id)
    {
        if (($model = TUser::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('User Data Not Found');
    }
}
