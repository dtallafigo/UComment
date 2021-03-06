<?php

use app\models\Seguidores;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Usuarios */

$this->title = 'Editar usuario ' . $model->log_us;
?>
<div class="row">
    <div class="col-12" style="border: 1px solid;">
        <div class="row com">
            <div class="col-1">
                <a href="<?= Url::to(Yii::$app->request->referrer); ?>">
                    <img src="icons/hacia-atras.png" id="flecha">
                </a>
            </div>
            <div class="col-10 d-flex justify-content-left">
                <h4 style="margin: 1% 0 0 0;">Editar perfil</h4>
            </div>
        </div>
        <div class="row user">
            <div class="col-sm-12 col-md-4 col-lg-4">
                <?php $form = ActiveForm::begin(); ?>
                <img src="<?= s3GetUrl($model->url_img, 'ucomment') ?>" id="perfil">
            </div>
            <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                <?= $form->field($model, 'log_us', ['options' => ['class' => 'edit-log_us']])->input(['maxlength' => true, 'value' => $model->log_us])->label(false) ?>
                <?= $form->field($model, 'url_img', ['options' => ['class' => 'file-input']])->fileInput()->label(false) ?>
            </div>
            <div class="col-4">
                <?= Html::submitButton('Editar', ['class' => 'edit']) ?>
            </div>
        </div>
        <div class="row bio">
            <div class="col-2 d-flex justify-content-center" style="text-align: center;">
                <img src="icons/bio.svg" id="bio">
            </div>
            <div class="col-9">
                <?= $form->field($model, 'bio')->textarea(['maxlength' => true, 'value' => $model->bio])->label(false) ?>
            </div>
        </div>
        <div class="row location">
            <div class="col-2 d-flex justify-content-center">
                <img src="icons/location.svg" id="location">
            </div>
            <div class="col-9">
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