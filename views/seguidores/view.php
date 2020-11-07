<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Seguidores */

$this->title = $model->seguidor_id;
$this->params['breadcrumbs'][] = ['label' => 'Seguidores', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="seguidores-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'seguidor_id' => $model->seguidor_id, 'seguido_id' => $model->seguido_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'seguidor_id' => $model->seguidor_id, 'seguido_id' => $model->seguido_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'seguidor_id',
            'seguido_id',
        ],
    ]) ?>

</div>
