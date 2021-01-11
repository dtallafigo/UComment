<?php

use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\ComentariosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Admin comentarios';
?>
<div class="row com">
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="col-12">
        <?php Pjax::begin(); ?>

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                'text',
                'created_at',
                'url_img',

                ['class' => 'yii\grid\ActionColumn',
                'template' => '{view} {delete}'],
            ],
        ]); ?>

        <?php Pjax::end(); ?>
    </div>
</div>