<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Bloqueados */

$this->title = 'Create Bloqueados';
$this->params['breadcrumbs'][] = ['label' => 'Bloqueados', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bloqueados-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
