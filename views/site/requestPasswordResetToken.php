<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\PasswordResetRequestForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Enviar email';
?>
<div class="row com">
    <div class="col-12">
        <h1><?= Html::encode('Recuperar contraseña') ?></h1>
    </div>
    <div class="col-12">
        <p>Por favor introduce tu email para poder mandarte un email para recuperar la contraseña</p>
    </div>
    <div class="col-12">
        <?php $form = ActiveForm::begin(['id' => 'request-password-reset-form']); ?>

        <?= $form->field($model, 'email')->textInput(['autofocus' => true]) ?>

        <div class="form-group">
            <?= Html::submitButton('Enviar', ['class' => 'log_button']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>