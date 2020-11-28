<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Comsave */

$this->title = 'Update Comsave: ' . $model->usuario_id;
$this->params['breadcrumbs'][] = ['label' => 'Comsaves', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->usuario_id, 'url' => ['view', 'usuario_id' => $model->usuario_id, 'comentario_id' => $model->comentario_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="comsave-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
