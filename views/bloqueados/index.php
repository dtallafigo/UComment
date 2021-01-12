<?php

use yii\bootstrap4\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\BloqueadosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Bloqueados';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bloqueados-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Bloqueados', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'usuario',
            'bloqueado',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
