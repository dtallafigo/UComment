<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

$this->title = 'Login';

?>

<div class="container">
    <div class="row com">
        <div class="col-sm-12 col-md-12 col-lg-6">
            <div class="col-12">
                <h2>Iniciar sesion en <img src="icons/logodavo2.png" alt="logo" id="logo-login"></h2>
            </div>
            <div class="col-12">
                <?php $form = ActiveForm::begin([
                    'id' => 'login-form',
                    'layout' => 'horizontal',
                    'fieldConfig' => [
                        'horizontalCssClasses' => ['wrapper' => 'col-lg-12'],
                    ],
                ]); ?>

                <?= $form->field($login, 'username')->textInput()->hint('Introduce un nombre de usuario ya registrado.')->label('Usuario') ?>
                <?= $form->field($login, 'password')->passwordInput()->hint('Introduce tu contraseña.')->label('Contraseña') ?>
                <?= Html::submitButton('Login', ['class' => 'log-button', 'name' => 'login-button']) ?>

                <?php ActiveForm::end(); ?>
            </div>
            <div class="col-12" style="margin-top: 20px;">
                <p><?= Html::a('¿Olvidate tu contraseña?', ['site/request-password-reset']) ?></p>
            </div>
        </div>
        <div class="col-sm-12 col-md-12 col-lg-6">
            <div class="col-12">
                <h2>Unete a <img src="icons/logodavo2.png" alt="logo" id="logo-login"></h2>
            </div>
            <div class="col-12">
                <?php $form = ActiveForm::begin([
                    'layout' => 'horizontal',
                    'fieldConfig' => [
                        'horizontalCssClasses' => ['wrapper' => 'col-lg-12'],
                    ],
                ]); ?>

                <?= $form->field($usuario, 'log_us')->textInput()->hint('Introduce tu nombre de ususario.')->label('Usuario') ?>
                <?= $form->field($usuario, 'email')->input('email')->hint('Introduce un email valido.')->label('Email') ?>
                <?= $form->field($usuario, 'password')->passwordInput()->hint('Introduce tu contraseña.')->label('Contraseña') ?>
                <?= $form->field($usuario, 'password_repeat')->passwordInput()->hint('Introduce de nuevo tu contraseña.')->label('Repetir contraseña') ?>
                <?= Html::submitButton('Registrar', ['class' => 'log-button']) ?>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>