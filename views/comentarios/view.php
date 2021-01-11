<?php

use app\models\Comentarios;
use app\models\Likes;
use app\models\Usuarios;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\helpers\Url;
use yii\bootstrap4\ButtonDropdown;
use app\models\Seguidores;

/* @var $this yii\web\View */
/* @var $model app\models\Comentarios */

$userA = Usuarios::findOne(['id' => $model->usuario_id]);
$userB = Usuarios::findOne(['id' => Yii::$app->user->id]);
$this->title = $userB->log_us . ' en UComment';
\yii\web\YiiAsset::register($this);
$seguir = Url::to(['seguidores/follow']);
$js2 = <<<'EOT'
const openEls = document.querySelectorAll("[data-open]");
const closeEls = document.querySelectorAll("[data-close]");
const isVisible = "is-visible";

for (const el of openEls) {
  el.addEventListener("click", function() {
    const modalId = this.dataset.open;
    document.getElementById(modalId).classList.add(isVisible);
  });
}

for (const el of closeEls) {
  el.addEventListener("click", function() {
    this.parentElement.parentElement.parentElement.classList.remove(isVisible);
  });
}

document.addEventListener("click", e => {
  if (e.target == document.querySelector(".modal.is-visible")) {
    document.querySelector(".modal.is-visible").classList.remove(isVisible);
  }
});

document.addEventListener("keyup", e => {
  // if we press the ESC
  if (e.key == "Escape" && document.querySelector(".modal.is-visible")) {
    document.querySelector(".modal.is-visible").classList.remove(isVisible);
  }
});
EOT;
$this->registerJs($js2);
$like = Url::to(['likes/like']);
$likes1 = <<<EOT
var boton = $("#like$model->id");
boton.click(function(event) {
event.preventDefault();
$.ajax({
    method: 'GET',
    url: '$like',
    data: {
        'comentario_id': $model->id
    },
    success: function (data, code, jqXHR) {
        var countlike$model->id = document.getElementById("countLike$model->id");
        countlike$model->id.innerHTML = data[1] + ' Likes';
        if (data[0]) {
            document.getElementById("icon$model->id").src="icons/like.svg";
        } else {
            document.getElementById("icon$model->id").src="icons/dislike.svg";
        }
    }
});
});
EOT;
$this->registerJs($likes1);
?>
<div class="row">
    <div class="col-sm-12 col-md-12 col-lg-8">
        <div class="row com">
            <div class="col-1">
                <a href="<?= Url::to(Yii::$app->request->referrer); ?>">
                    <img src="icons/hacia-atras.png" id="flecha">
                </a>
            </div>
            <div class="col-10">
                <h4>Comentar</h4>
            </div>
        </div>
        <?php if ($model->respuesta) : ?>
            <?php
            $original = Comentarios::findOne(['id' => $model->respuesta]);
            $uco = Usuarios::findOne(['id' => $original->usuario_id]);
            $likes2 = <<<EOT
            var boton = $("#like$original->id");
            boton.click(function(event) {
            event.preventDefault();
            $.ajax({
                method: 'GET',
                url: '$like',
                data: {
                    'comentario_id': $original->id
                },
                success: function (data, code, jqXHR) {
                    var countlike$original->id = document.getElementById("countLike$original->id");
                    countlike$original->id.innerHTML = data[1];
                    if (data[0]) {
                        document.getElementById("icon$original->id").src="icons/like.svg";
                    } else {
                        document.getElementById("icon$original->id").src="icons/dislike.svg";
                    }
                }
            });
            });
            EOT;
            $this->registerJs($likes2);
            ?>
            <div class="modal" id="respuesta<?= $original->id ?>">
                <div class="modal-dialog">
                    <header class="modal-header">
                        <img src="<?= s3GetUrl($userB->url_img, 'ucomment') ?>" id="inicio">
                        <h4><?= $userB->log_us ?></h4>
                        <button class="close-modal" aria-label="close modal" data-close>
                            ✕
                        </button>
                    </header>
                    <section class="modal-content">
                        <div class="card">
                            <div class="card-header">
                                <div class="col-12 d-flex justify-content-center">
                                    <h4>Responder Comentario</h2>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="row">
                                            <div class="col-2 d-flex justify-content-center">
                                                <img src="<?= s3GetUrl($uco->url_img, 'ucomment') ?>" id="citado">
                                            </div>
                                            <div class="col-10 d-flex justify-content-left">
                                                <p class="center"><?= $uco->log_us ?> · <?= $original->fecha($original->created_at) ?></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <p><?= $original->url($original->text) ?></p>
                                        <?php if ($original->url_img) : ?>
                                            <div class="col-12 d-flex justify-content-right img">
                                                <img src="<?= s3GetUrl($original->url_img, 'ucomment') ?>" alt="">
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="card" style="margin-top: 3%;">
                                    <div class="card-body">
                                        <?php $form = ActiveForm::begin(); ?>
                                        <?= $form->field($publicacion, 'text')->textarea(['maxlength' => true, 'placeholder' => 'Publica algo...'])->label(false) ?>
                                        <?= $form->field($publicacion, 'respuesta')->hiddenInput(['value' => $original->id])->label(false); ?>
                                        <?= $form->field($publicacion, 'url_img', ['options' => ['class' => '']])->fileInput()->label(false) ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 d-flex flex-row-reverse" style="margin-top: 3%;">
                                        <?= Html::submitButton('Publicar', ['class' => 'log-button']) ?>
                                        <?php ActiveForm::end(); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
            <div class="modal" id="citado<?= $original->id ?>">
                <div class="modal-dialog">
                    <header class="modal-header">
                        <img src="<?= s3GetUrl($userB->url_img, 'ucomment') ?>" id="inicio">
                        <h4><?= $userB->log_us ?></h4>
                        <button class="close-modal" aria-label="close modal" data-close>
                            ✕
                        </button>
                    </header>
                    <section class="modal-content">
                        <div class="card">
                            <div class="card-header">
                                <div class="col-12 d-flex justify-content-center">
                                    <h4>Citar Comentario</h2>
                                </div>
                            </div>
                            <div class="card-body">
                                <?php $form = ActiveForm::begin(); ?>
                                <?= $form->field($publicacion, 'text')->textarea(['maxlength' => true, 'placeholder' => 'Cita este comentario...'])->label(false) ?>
                                <?= $form->field($publicacion, 'citado')->hiddenInput(['value' => $original->id])->label(false); ?>
                                <?= $form->field($publicacion, 'url_img', ['options' => ['class' => '']])->fileInput()->label(false) ?>
                                <div class="card" style="margin-top: 2%;">
                                    <div class="card-header">
                                        <div class="row">
                                            <div class="col-2 d-flex justify-content-center">
                                                <img src="<?= s3GetUrl($uco->url_img, 'ucomment') ?>" id="citado">
                                            </div>
                                            <div class="col-10 d-flex justify-content-left">
                                                <p class="center"><?= $uco->log_us ?> · <?= $original->fecha($original->created_at) ?></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <p class="card-text"><?= $original->url($original->text) ?></p>
                                        <?php if ($original->url_img) : ?>
                                            <div class="col-12 d-flex justify-content-right img">
                                                <img src="<?= s3GetUrl($original->url_img, 'ucomment') ?>" alt="">
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="d-flex flex-row-reverse" style="margin-top: 4%;">
                                    <?= Html::submitButton('Publicar', ['class' => 'log-button']) ?>
                                    <?php ActiveForm::end(); ?>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
            <div class="row com justify-content-center">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-2 d-flex justify-content-center">
                                <img src="<?= s3GetUrl($uco->url_img, 'ucomment') ?>" alt="" id="fcom">
                            </div>
                            <div class="col-6 d-flex justify-content-left">
                                <p class="center"><?= $uco->log_us ?> · <?= $original->fecha($original->created_at) ?></p>
                            </div>
                            <?php if ($original->usuario_id == Yii::$app->user->id) : ?>
                                <div class="col-4 d-flex flex-row-reverse">
                                    <?= ButtonDropdown::widget([
                                        'options' => ['class' => 'delete-comment'],
                                        'direction' => 'left',
                                        'label' => ' ···',
                                        'dropdown' => [
                                            'items' => [
                                                Html::beginForm(['/comentarios/delete', 'id' => $original->id], 'post') . Html::img('icons/papelera.png', ['id' => 'papelera']) . Html::submitButton('Eliminar comentario', ['class' => 'eliminar-comentario']) . Html::endForm()
                                            ],
                                        ]
                                    ]) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="card-body">
                        <a href="<?= Url::to(['comentarios/view', 'id' => $original->id]); ?>">
                            <div class="col-12">
                                <p class="card-text"><?= $original->url($original->text) ?></p>
                                <?php if ($original->url_img) : ?>
                                    <div class="col-12 d-flex justify-content-right img">
                                        <img src="<?= s3GetUrl($original->url_img, 'ucomment') ?>" alt="">
                                    </div>
                                <?php endif; ?>
                            </div>
                        </a>
                    </div>
                    <?php if ($original->citado) : ?>
                        <?php $citado = Comentarios::find()->where(['id' => $original->citado])->one(); ?>
                        <?php $uc = Usuarios::find()->where(['id' => $citado->usuario_id])->one(); ?>
                        <div class="card-body">
                            <div class="card" style="margin-bottom: 2%;">
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col-2 d-flex justify-content-center">
                                            <img src="<?= $uc->url_img ?>" id="citado">
                                        </div>
                                        <div class="col-10 d-flex justify-content-left">
                                            <p class="center"><?= $uc->log_us ?> · <?= $citado->fecha($citado->created_at) ?></p>
                                        </div>
                                    </div>
                                </div>
                                <a href="<?= Url::to(['comentarios/view', 'id' => $citado->id]); ?>" id="comentario">
                                    <div class="card-body">
                                        <p class="card-text"><?= $citado->url($citado->text) ?></p>
                                        <?php if ($citado->url_img) : ?>
                                            <div class="col-12 d-flex justify-content-right img">
                                                <img src="<?= s3GetUrl($citado->url_img, 'ucomment') ?>" alt="">
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-3 d-flex justify-content-center">
                                <a class="open-modal" data-open="respuesta<?= $original->id ?>">
                                    <img src="icons/respuesta.svg" class="icon" id="answer">
                                </a>
                                <p class="count"><?= $original->getComentarios()->count(); ?></p>
                            </div>
                            <div class="col-3 d-flex justify-content-center">
                                <a class="open-modal" data-open="citado<?= $original->id ?>">
                                    <img src="icons/citado.svg" class="icon" id="citar">
                                </a>
                                <p class="count"><?= $original->getCitados()->count(); ?></p>
                            </div>
                            <div class="col-3 d-flex justify-content-center">
                                <a id="like<?= $original->id ?>" class="heart">
                                    <img src="<?= Likes::like($original->id) ? 'icons/like.svg' : 'icons/dislike.svg' ?>" class="icon" id="icon<?= $original->id ?>">
                                </a>
                                <p id="countLike<?= $original->id ?>" class="count"><?= Likes::find()->where(['comentario_id' => $original->id])->count() ?></p>
                            </div>
                            <div class="col-3">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal" id="respuesta<?= $model->id ?>">
                <div class="modal-dialog">
                    <header class="modal-header">
                        <img src="<?= s3GetUrl($userB->url_img, 'ucomment') ?>" id="inicio">
                        <h4><?= $userB->log_us ?></h4>
                        <button class="close-modal" aria-label="close modal" data-close>
                            ✕
                        </button>
                    </header>
                    <section class="modal-content">
                        <div class="card">
                            <div class="card-header">
                                <div class="col-12 d-flex justify-content-center">
                                    <h4>Responder Comentario</h2>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="row">
                                            <div class="col-2 d-flex justify-content-center">
                                                <img src="<?= s3GetUrl($userA->url_img, 'ucomment') ?>" id="citado">
                                            </div>
                                            <div class="col-10 d-flex justify-content-left">
                                                <p class="center"><?= $userA->log_us ?> · <?= $model->fecha($model->created_at) ?></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <p class="card-text"><?= $model->url($model->text) ?></p>
                                        <?php if ($model->url_img) : ?>
                                            <div class="col-12 d-flex justify-content-right img">
                                                <img src="<?= s3GetUrl($model->url_img, 'ucomment') ?>" alt="">
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="card" style="margin-top: 3%;">
                                    <div class="card-body">
                                        <?php $form = ActiveForm::begin(); ?>
                                        <?= $form->field($publicacion, 'text')->textarea(['maxlength' => true, 'placeholder' => 'Publica algo...'])->label(false) ?>
                                        <?= $form->field($publicacion, 'respuesta')->hiddenInput(['value' => $model->id])->label(false); ?>
                                        <?= $form->field($publicacion, 'url_img', ['options' => ['class' => '']])->fileInput()->label(false) ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 d-flex flex-row-reverse" style="margin-top: 3%;">
                                        <?= Html::submitButton('Publicar', ['class' => 'log-button']) ?>
                                        <?php ActiveForm::end(); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
            <div class="modal" id="citado<?= $model->id ?>">
                <div class="modal-dialog">
                    <header class="modal-header">
                        <img src="<?= s3GetUrl($userB->url_img, 'ucomment') ?>" id="inicio">
                        <h4><?= $userB->log_us ?></h4>
                        <button class="close-modal" aria-label="close modal" data-close>
                            ✕
                        </button>
                    </header>
                    <section class="modal-content">
                        <div class="card">
                            <div class="card-header">
                                <div class="col-12 d-flex justify-content-center">
                                    <h4>Citar Comentario</h2>
                                </div>
                            </div>
                            <div class="card-body">
                                <?php $form = ActiveForm::begin(); ?>
                                <?= $form->field($publicacion, 'text')->textarea(['maxlength' => true, 'placeholder' => 'Cita este comentario...'])->label(false) ?>
                                <?= $form->field($publicacion, 'citado')->hiddenInput(['value' => $model->id])->label(false); ?>
                                <?= $form->field($publicacion, 'url_img', ['options' => ['class' => '']])->fileInput()->label(false) ?>
                                <div class="card" style="margin-top: 2%;">
                                    <div class="card-header">
                                        <div class="row">
                                            <div class="col-2 d-flex justify-content-center">
                                                <img src="<?= s3GetUrl($userA->url_img, 'ucomment') ?>" id="citado">
                                            </div>
                                            <div class="col-10 d-flex justify-content-left">
                                                <p class="center"><?= $userA->log_us ?> · <?= $model->fecha($model->created_at) ?></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <p class="card-text"><?= $model->url($model->text) ?></p>
                                        <?php if ($model->url_img) : ?>
                                            <div class="col-12 d-flex justify-content-right img">
                                                <img src="<?= s3GetUrl($model->url_img, 'ucomment') ?>" alt="">
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="d-flex flex-row-reverse" style="margin-top: 4%;">
                                    <?= Html::submitButton('Publicar', ['class' => 'log-button']) ?>
                                    <?php ActiveForm::end(); ?>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
            <div class="row com justify-content-center">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-2 d-flex justify-content-center">
                                <img src="<?= s3GetUrl($userB->url_img, 'ucomment') ?>" alt="" id="fcom">
                            </div>
                            <div class="col-6 d-flex justify-content-left">
                                <p class="center"><?= $userA->log_us ?> · <?= $model->fecha($model->created_at) ?></p>
                            </div>
                            <?php if ($model->usuario_id == Yii::$app->user->id) : ?>
                                <div class="col-4 d-flex flex-row-reverse">
                                    <?= ButtonDropdown::widget([
                                        'options' => ['class' => 'delete-comment'],
                                        'direction' => 'left',
                                        'label' => ' ···',
                                        'dropdown' => [
                                            'items' => [
                                                Html::beginForm(['/comentarios/delete', 'id' => $model->id], 'post') . Html::img('icons/papelera.png', ['id' => 'papelera']) . Html::submitButton('Eliminar comentario', ['class' => 'eliminar-comentario']) . Html::endForm()
                                            ],
                                        ]
                                    ]) ?>
                                </div>
                            <?php endif; ?>
                            <div class="col-12" style="margin-top: 3%;">
                                <small>Respuesta a <a href="<?= Url::to(['usuarios/view', 'id' => $uco->id]); ?>"><?= $uco->log_us ?></a></small>
                            </div>
                            <div class="col-12" style="margin-top: 3%; margin-bottom: 3%;">
                                <a href="<?= Url::to(['comentarios/view', 'id' => $model->id]); ?>">
                                    <p class="card-text"><?= $model->url($model->text) ?></p>
                                    <?php if ($model->url_img) : ?>
                                        <div class="col-12 d-flex justify-content-right img">
                                            <img src="<?= s3GetUrl($model->url_img, 'ucomment') ?>" alt="">
                                        </div>
                                    <?php endif; ?>
                                </a>
                            </div>
                        </div>
                        <?php if ($model->citado) : ?>
                            <?php $citado = Comentarios::find()->where(['id' => $model->citado])->one(); ?>
                            <?php $uc = Usuarios::find()->where(['id' => $citado->usuario_id])->one(); ?>
                            <div class="card-body">
                                <div class="card" style="margin-bottom: 2%;">
                                    <div class="card-header">
                                        <div class="row">
                                            <div class="col-2 d-flex justify-content-center">
                                                <img src="<?= $uc->url_img ?>" id="citado">
                                            </div>
                                            <div class="col-8 d-flex justify-content-left">
                                                <a href="<?= Url::to(['usuarios/view', 'id' => $uc->id]); ?>">
                                                    <p class="card-title"><?= $uc->log_us ?> · <?= $citado->fecha($citado->created_at) ?></p>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <a href="<?= Url::to(['comentarios/view', 'id' => $citado->id]); ?>" id="comentario">
                                        <div class="card-body">
                                            <p class="card-text"><?= $citado->text ?></p>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-3 d-flex justify-content-center">
                                    <p class="count"><?= $model->getComentarios()->count(); ?> Respuesta</p>
                                </div>
                                <div class="col-3 d-flex justify-content-center">
                                    <p class="count"><?= $model->getCitados()->count(); ?> Citados</p>
                                </div>
                                <div class="col-3 d-flex justify-content-center">
                                    <a href="<?= Url::to(['likes/view', 'comentario_id' => $model->id]); ?>" id="link_like">
                                        <p id="countLike<?= $model->id ?>" class="count"><?= Likes::find()->where(['comentario_id' => $model->id])->count() ?> Likes</p>
                                    </a>
                                </div>
                                <div class="col-3">

                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-3 d-flex justify-content-center">
                                    <a class="open-modal" data-open="respuesta<?= $model->id ?>">
                                        <img src="icons/respuesta.svg" class="icon" id="answer">
                                    </a>
                                </div>
                                <div class="col-3 d-flex justify-content-center">
                                    <a class="open-modal" data-open="citado<?= $model->id ?>">
                                        <img src="icons/citado.svg" class="icon" id="citar">
                                    </a>
                                </div>
                                <div class="col-3 d-flex justify-content-center">
                                    <a id="like<?= $model->id ?>" class="heart">
                                        <img src="<?= Likes::like($model->id) ? 'icons/like.svg' : 'icons/dislike.svg' ?>" class="icon" id="icon<?= $model->id ?>">
                                    </a>
                                </div>
                                <div class="col-3">

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php else : ?>
            <div class="modal" id="respuesta<?= $model->id ?>">
                <div class="modal-dialog">
                    <header class="modal-header">
                        <img src="<?= s3GetUrl($userB->url_img, 'ucomment') ?>" id="inicio">
                        <h4><?= $userB->log_us ?></h4>
                        <button class="close-modal" aria-label="close modal" data-close>
                            ✕
                        </button>
                    </header>
                    <section class="modal-content">
                        <div class="card">
                            <div class="card-header">
                                <div class="col-12 d-flex justify-content-center">
                                    <h4>Responder Comentario</h2>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="row">
                                            <div class="col-2 d-flex justify-content-center">
                                                <img src="<?= s3GetUrl($userA->url_img, 'ucomment') ?>" id="citado">
                                            </div>
                                            <div class="col-8 d-flex justify-content-left">
                                                <p class="card-title"><?= $userA->log_us ?> · <?= $model->fecha($model->created_at) ?></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <p class="card-text"><?= $model->url($model->text) ?></p>
                                        <?php if ($model->url_img) : ?>
                                            <div class="col-12 d-flex justify-content-right img">
                                                <img src="<?= s3GetUrl($model->url_img, 'ucomment') ?>" alt="">
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="card" style="margin-top: 3%;">
                                    <div class="card-body">
                                        <?php $form = ActiveForm::begin(); ?>
                                        <?= $form->field($publicacion, 'text')->textarea(['maxlength' => true, 'placeholder' => 'Publica algo...'])->label(false) ?>
                                        <?= $form->field($publicacion, 'respuesta')->hiddenInput(['value' => $model->id])->label(false); ?>
                                        <?= $form->field($publicacion, 'url_img', ['options' => ['class' => '']])->fileInput()->label(false) ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 d-flex flex-row-reverse" style="margin-top: 3%;">
                                        <?= Html::submitButton('Publicar', ['class' => 'log-button']) ?>
                                        <?php ActiveForm::end(); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
            <div class="modal" id="citado<?= $model->id ?>">
                <div class="modal-dialog">
                    <header class="modal-header">
                        <img src="<?= s3GetUrl($userB->url_img, 'ucomment') ?>" id="inicio">
                        <h4><?= $userB->log_us ?></h4>
                        <button class="close-modal" aria-label="close modal" data-close>
                            ✕
                        </button>
                    </header>
                    <section class="modal-content">
                        <div class="card">
                            <div class="card-header">
                                <div class="col-12 d-flex justify-content-center">
                                    <h4>Citar Comentario</h2>
                                </div>
                            </div>
                            <div class="card-body">
                                <?php $form = ActiveForm::begin(); ?>
                                <?= $form->field($publicacion, 'text')->textarea(['maxlength' => true, 'placeholder' => 'Cita este comentario...'])->label(false) ?>
                                <?= $form->field($publicacion, 'citado')->hiddenInput(['value' => $model->id])->label(false); ?>
                                <?= $form->field($publicacion, 'url_img', ['options' => ['class' => '']])->fileInput()->label(false) ?>
                                <div class="card" style="margin-top: 2%;">
                                    <div class="card-header">
                                        <div class="row">
                                            <div class="col-2 d-flex justify-content-center">
                                                <img src="<?= s3GetUrl($userA->url_img, 'ucomment') ?>" id="citado">
                                            </div>
                                            <div class="col-6 d-flex justify-content-left">
                                                <p class="card-title"><?= $userA->log_us ?> . <?= $model->fecha($model->created_at) ?></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <p class="card-text"><?= $model->url($model->text) ?></p>
                                        <?php if ($model->url_img) : ?>
                                            <div class="col-12 d-flex justify-content-right img">
                                                <img src="<?= s3GetUrl($model->url_img, 'ucomment') ?>" alt="">
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="d-flex flex-row-reverse" style="margin-top: 4%;">
                                    <?= Html::submitButton('Publicar', ['class' => 'log-button']) ?>
                                    <?php ActiveForm::end(); ?>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
            <div class="row com justify-content-center">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-2 d-flex justify-content-center">
                                <img src="<?= s3GetUrl($userA->url_img, 'ucomment') ?>" alt="" id="fcom">
                            </div>
                            <div class="col-6 justify-content-left">
                                <a href="<?= Url::to(['usuarios/view', 'id' => $userA->id]); ?>" id="link_name">
                                    <h5><?= $userA->log_us ?> · <?= $model->fecha($model->created_at) ?></h5>
                                </a>
                            </div>
                            <?php if ($model->usuario_id == Yii::$app->user->id) : ?>
                                <div class="col-4 d-flex flex-row-reverse">
                                    <?= ButtonDropdown::widget([
                                        'options' => ['class' => 'delete-comment'],
                                        'direction' => 'left',
                                        'label' => ' ···',
                                        'dropdown' => [
                                            'items' => [
                                                Html::beginForm(['/comentarios/delete', 'id' => $model->id], 'post') . Html::img('icons/papelera.png', ['id' => 'papelera']) . Html::submitButton('Eliminar comentario', ['class' => 'eliminar-comentario']) . Html::endForm()
                                            ],
                                        ]
                                    ]) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-12" style="margin-top: 3%;">
                            <p><?= $model->url($model->text) ?></p>
                            <?php if ($model->url_img) : ?>
                                <div class="col-12 d-flex justify-content-right img">
                                    <img src="<?= s3GetUrl($model->url_img, 'ucomment') ?>" alt="">
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php if ($model->citado) : ?>
                        <?php $citado = Comentarios::find()->where(['id' => $model->citado])->one(); ?>
                        <?php $uc = Usuarios::find()->where(['id' => $citado->usuario_id])->one(); ?>
                        <div class="card-body">
                            <div class="card" style="margin-bottom: 2%;">
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col-1 d-flex justify-content-center">
                                            <img src="<?= s3GetUrl($uc->url_img, 'ucomment') ?>" id="citado">
                                        </div>
                                        <div class="col-6 d-flex justify-content-left">
                                            <a href="<?= Url::to(['usuarios/view', 'id' => $uc->id]); ?>">
                                                <p class="card-title"><?= $uc->log_us ?> · <?= $citado->fecha($citado->created_at) ?></p>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <a href="<?= Url::to(['comentarios/view', 'id' => $citado->id]); ?>" id="comentario">
                                    <div class="card-body">
                                        <p class="card-text"><?= $citado->url($citado->text) ?></p>
                                        <?php if ($citado->url_img) : ?>
                                            <div class="col-12 d-flex justify-content-right img">
                                                <img src="<?= s3GetUrl($citado->url_img, 'ucomment') ?>" alt="">
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-3 d-flex justify-content-center">
                                <p class="count"><?= $model->getComentarios()->count(); ?> Respuesta</p>
                            </div>
                            <div class="col-3 d-flex justify-content-center">
                                <p class="count"><?= $model->getCitados()->count(); ?> Citados</p>
                            </div>
                            <div class="col-3 d-flex justify-content-center">
                                <a href="<?= Url::to(['likes/view', 'comentario_id' => $model->id]); ?>" id="link_like">
                                    <p id="countLike<?= $model->id ?>" class="count"><?= Likes::find()->where(['comentario_id' => $model->id])->count() ?> Likes</p>
                                </a>
                            </div>
                            <div class="col-3">

                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-3 d-flex justify-content-center">
                                <a class="open-modal" data-open="respuesta<?= $model->id ?>">
                                    <img src="icons/respuesta.svg" class="icon" id="answer">
                                </a>
                            </div>
                            <div class="col-3 d-flex justify-content-center">
                                <a class="open-modal" data-open="citado<?= $model->id ?>">
                                    <img src="icons/citado.svg" class="icon" id="citar">
                                </a>
                            </div>
                            <div class="col-3 d-flex justify-content-center">
                                <a id="like<?= $model->id ?>" class="heart">
                                    <img src="<?= Likes::like($model->id) ? 'icons/like.svg' : 'icons/dislike.svg' ?>" class="icon" id="icon<?= $model->id ?>">
                                </a>
                            </div>
                            <div class="col-3">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <div class="col-sm-12 col-md-12 col-lg-4 b">
        <?php
        $textS = Seguidores::siguiendo($userComment->id) ? 'Siguiendo' : 'Seguir';
        $jsS = <<<EOT
        var boton = $("#us$userComment->id");
        boton.click(function(event) {
            event.preventDefault();
            $.ajax({
                method: 'GET',
                url: '$seguir',
                data: {
                    'seguido_id': $userComment->id
                },
                success: function (data, code, jqXHR) {
                    var text = ''
                    if (data[0])
                        text = 'Siguiendo'
                    else
                        text = 'Seguir'
        
                    var us$userComment->id = document.getElementById("us$userComment->id")
                    us$userComment->id.innerHTML = text;
            }
            });
        });
        EOT;
        $this->registerJs($jsS);
        ?>
        <div class="card">
            <div class="card-header d-flex justify-content-center">
                <h5>Personas de interes</h5>
            </div>
            <div class="card-body s">
                <div class="row">
                    <div class="col-2 d-flex justify-content-center">
                        <img src="<?= s3GetUrl($userComment->url_img, 'ucomment') ?>" id="sugerido-img">
                    </div>
                    <div class="col-4 d-flex justify-content-left">
                        <a href="<?= Url::to(['usuarios/view', 'id' => $userComment->id]); ?>">
                            <p class="center"><?= $userComment->log_us ?></p>
                        </a>
                    </div>
                    <div class="col-2 d-flex flex-row">
                        <?php if ($userComment->id != Yii::$app->user->id) : ?>
                            <?= Html::a($textS, ['seguidores/follow', 'seguido_id' => $userComment->id], ['class' => 'sbutton', 'id' => 'us' . $userComment->id]) ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php $answer = Comentarios::find()->where(['id' => $model->respuesta])->one(); ?>
            <?php if ($model->respuesta && $answer->usuario_id != $userComment->id) : ?>
                <?php
                $userRespuesta = Usuarios::find()->where(['id' => $answer->usuario_id])->one();
                $textS = Seguidores::siguiendo($userRespuesta->id) ? 'Siguiendo' : 'Seguir';
                $jsS = <<<EOT
                var boton = $("#ur$userRespuesta->id");
                boton.click(function(event) {
                    event.preventDefault();
                    $.ajax({
                        method: 'GET',
                        url: '$seguir',
                        data: {
                            'seguido_id': $userRespuesta->id
                        },
                        success: function (data, code, jqXHR) {
                            var text = ''
                            if (data[0])
                                text = 'Siguiendo'
                            else
                                text = 'Seguir'
                
                            var ur$userRespuesta->id = document.getElementById("ur$userRespuesta->id")
                            ur$userRespuesta->id.innerHTML = text;
                    }
                    });
                });
                EOT;
                $this->registerJs($jsS);
                ?>
                <div class="card-body s">
                    <div class="row">
                        <div class="col-2 d-flex justify-content-center">
                            <img src="<?= $userRespuesta->url_img ?>" id="sugerido-img">
                        </div>
                        <div class="col-4 d-flex justify-content-left">
                            <a href="<?= Url::to(['usuarios/view', 'id' => $userRespuesta->id]); ?>">
                                <p class="center"><?= $userRespuesta->log_us ?></p>
                            </a>
                        </div>
                        <div class="col-2 d-flex flex-row">
                            <?php if ($userRespuesta->id != Yii::$app->user->id) : ?>
                                <?= Html::a($textS, ['seguidores/follow', 'seguido_id' => $userRespuesta->id], ['class' => 'sbutton', 'id' => 'ur' . $userRespuesta->id]) ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>