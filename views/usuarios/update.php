<?php

use app\models\Seguidores;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Usuarios */

$this->title = 'Update Usuarios: ' . $model->id;
?>
<div class="row">
    <div class="col-9" style="border: 1px solid;">
        <div class="row user">
            <div class="col-sm-12 col-md-4 col-lg-4">
            <?php $form = ActiveForm::begin(); ?>
                <img src="<?= $model->url_img ?>" id="perfil">
            </div>
            <div class="col-sm-12 col-md-8 col-lg-4">
                <?= $form->field($model, 'log_us')->input(['maxlength' => true, 'class' => 'usuario', 'value' => $model->log_us])->label(false) ?>
            </div>
            <div class="col-12 d-flex flex-row-reverse">
                <?= Html::submitButton('Editar', ['class' => 'btn btn-primary']) ?>   
            </div>
        </div>
        <div class="row bio">
            <div class="col-2 d-flex justify-content-center" style="text-align: center;">
                <img src="icons/bio.svg" id="bio">
            </div>
            <div class="col-10">
                <?= $form->field($model, 'bio')->input(['maxlength' => true, 'value' => $model->bio])->label(false) ?>
            </div>
        </div>
        <div class="row location">
            <div class="col-2 d-flex justify-content-center">
                <img src="icons/location.svg" id="location">
            </div>
            <div class="col-10">
                <?= $form->field($model, 'ubi')->input(['maxlength' => true, 'value' => $model->ubi])->label(false) ?>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
        <div class="row sg">
            <div class="col-3 d-flex justify-content-center">
                <p id="sg"><?= Seguidores::find()->where(['seguido_id' => $model->id])->count() ?></p>
            </div>
            <div class="col-3">
                <h5>Seguidores</h5>
            </div>
            <div class="col-3 d-flex justify-content-center">
                <p><?= Seguidores::find()->where(['seguidor_id' => $model->id])->count() ?></p>
            </div>
            <div class="col-3">
                <h5>Seguidos</h5>
            </div>
        </div>
    </div>
</div>