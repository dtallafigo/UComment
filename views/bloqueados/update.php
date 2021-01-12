<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Bloqueados */

$this->title = 'Update Bloqueados: ' . $model->usuario;
$this->params['breadcrumbs'][] = ['label' => 'Bloqueados', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->usuario, 'url' => ['view', 'usuario' => $model->usuario, 'bloqueado' => $model->bloqueado]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="bloqueados-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
