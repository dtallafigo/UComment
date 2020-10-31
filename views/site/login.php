<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

$this->title = 'Login';

?>

<div class="container">
    <div class="row">
        <div class="col-6">
            <div class="col-12">
                <h1>Iniciar Sesion</h1>
            </div>
            <div class="col-12">
                <?php $form = ActiveForm::begin([
                    'id' => 'login-form',
                    'layout' => 'horizontal',
                    'fieldConfig' => [
                        'horizontalCssClasses' => ['wrapper' => 'col-lg-12'],
                        'template' => "{label}\n{hint}\n{input}",
                    ],
                ]); ?>

                    <div class="col-12">
                        <?= $form->field($login, 'username')->textInput()->hint('Introduce un nombre de usuario ya registrado.')->label('Usuario') ?>
                        <?= $form->field($login, 'password')->passwordInput()->hint('Introduce tu contraseña.')->label('Contraseña') ?>
                        <?= Html::submitButton('Login', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
                    </div>
                    
                <?php ActiveForm::end(); ?>
            </div>
        </div>
        <div class="col-6">
            <div class="col-12">
                    <h1>Unete a UComment</h1>
                    <div class="col-12">
                    <?php $form = ActiveForm::begin([
                        'layout' => 'horizontal',
                        'fieldConfig' => [
                            'horizontalCssClasses' => ['wrapper' => 'col-lg-12'],
                            'template' => "{label}\n{hint}\n{input}",
                        ],
                    ]); ?>

                    <div class="col-12 flex">
                        <?= $form->field($usuario, 'log_us')->textInput()->hint('Introduce tu nombre de ususario.')->label('Usuario') ?>
                        <?= $form->field($usuario, 'password')->passwordInput()->hint('Introduce tu contraseña.')->label('Contraseña') ?>
                        <?= $form->field($usuario, 'password_repeat')->passwordInput()->hint('Introduce de nuevo tu contraseña.')->label('Repetir contraseña') ?>
                        <?= $form->field($usuario, 'email')->input('email')->hint('Introduce un email valido.')->label('Email') ?>
                        <?= Html::submitButton('Registrar', ['class' => 'btn btn-primary']) ?>
                    </div>
                    
                    <?php ActiveForm::end(); ?>
            </div>
            </div>
        </div>
    </div>
</div>