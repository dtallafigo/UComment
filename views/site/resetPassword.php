<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\ResetPasswordForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Recuperar contraseña';
?>
<div class="row com">
    <div class="col-12">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>
    <div class="col-12">
        <p>Introduce tu nueva contraseña</p>
    </div>
    <div class="col-lg-12">
        <?php $form = ActiveForm::begin(['id' => 'reset-password-form']); ?>

        <?= $form->field($model, 'password')->passwordInput(['autofocus' => true]) ?>

        <div class="form-group">
            <?= Html::submitButton('Guardar', ['class' => 'log-button']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>