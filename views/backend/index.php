<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Users';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tuser-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

  <?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
      ['class' => 'yii\grid\SerialColumn'],
      'full_name',
      'email',
      'gender',
      'age',
      'social_id',
      'social_media',
      'phone_number',
      [
        'header' => 'Status',
        'format' => 'raw',
        'value' => function($model){
          if ($model->status == $model::STATUS_ENABLE) {
            $checked = true;
            $value   = $model::STATUS_DISABLE;
            $label   = 'Enable';
          }else{
            $checked = false;
            $value   = $model::STATUS_ENABLE;
            $label   = 'Disable';
          }
          $response = '<div>'.$label.'</div>
            <div class="material-switch">
              '.Html::checkbox('status_'.$model->id, $checked, ['id'=>'checkbox-'.$model->id,'value' => $value,'data-id'=>$model->id,'class'=>'checkbox-change-status']).'
              <label for="checkbox-'.$model->id.'" class="label-success"></label>
            </div>';
          return $response;
        }
      ],
    ],
  ]); ?>
</div>
<?php
$this->registerJs('
$(".checkbox-change-status").click(function(){
  var dataId    = $(this).data("id");
  var dataValue = $(this).val();
  $.ajax({
    url : "'.Url::to(['/backend/change-status']).'?id="+dataId,
    type: "POST",
    data: {
      status : dataValue
    },
    success: function(data){
      location.reload();
    },
    error: function(){
      alert("Error");
      location.reload();
    }
  });
});
  ', \yii\web\View::POS_READY);
 ?>