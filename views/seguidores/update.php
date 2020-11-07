<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Seguidores */

$this->title = 'Update Seguidores: ' . $model->seguidor_id;
$this->params['breadcrumbs'][] = ['label' => 'Seguidores', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->seguidor_id, 'url' => ['view', 'seguidor_id' => $model->seguidor_id, 'seguido_id' => $model->seguido_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="seguidores-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
