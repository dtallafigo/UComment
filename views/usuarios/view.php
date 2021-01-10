<?php

use app\models\Comentarios;
use yii\bootstrap4\Html;
use yii\helpers\Url;
use yii\bootstrap4\ActiveForm;
use app\models\Seguidores;
use app\models\Usuarios;
use app\models\Likes;
use yii\bootstrap4\ButtonDropdown;
use yii\bootstrap4\LinkPager;

/* @var $this yii\web\View */
/* @var $model app\models\Usuarios */

$this->title = 'Perfil de ' . $usuario->log_us;
$text = Seguidores::siguiendo($usuario->id) ? 'Siguiendo' : 'Seguir';
$seguir = Url::to(['seguidores/follow']);
$cc = Comentarios::find()->where(['usuario_id' => $usuario->id])->count();
if ($cc == 0) {
    $cc = 0;
} else {
    $cc--;
}
$js1 = <<<EOT
var boton = $("#siguiendo");
var sg = $("#sg");
boton.click(function(event) {
    event.preventDefault();
    $.ajax({
        method: 'GET',
        url: '$seguir',
        data: {
            'seguido_id': $usuario->id
        },
        success: function (data, code, jqXHR) {
            var text = ''
            if (data[0])
                text = 'Siguiendo'
            else
                text = 'Seguir'

            var seguidores = document.getElementById("siguiendo")
            var sg = document.getElementById("sg")
            sg.innerHTML = data[1];
            seguidores.innerHTML = text;
    }
    });
});
EOT;
$this->registerJs($js1);
$js2 = <<<EOT
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
$save = Url::to(['comsave/save']);

?>
<div class="row g">
    <div class="col-sm-12 col-md-12 col-lg-8">
        <div class="row com">
            <div class="col-1">
                <a href="<?= Url::to(Yii::$app->request->referrer); ?>">
                    <img src="icons/hacia-atras.png" id="flecha">
                </a>
            </div>
            <div class="col-10 d-flex justify-content-left">
                <h4><?= $usuario->log_us ?></h4>
            </div>
            <div class="col-4" style="margin-left: 8%;">
                <small><?= $cc ?> comentarios publicados</small>
            </div>
        </div>
        <div class="row user">
            <div class="col-sm-12 col-md-4 col-lg-4 d-flex justify-content-center align-self-center text-center">
                <img src="<?= s3GetUrl($usuario->url_img, 'ucomment') ?>" id="perfil">
            </div>
            <div class="col-sm-12 col-md-8 col-lg-8">
                <?php if (Seguidores::find()->where(['seguidor_id' => $usuario->id])->andWhere(['seguido_id' => Yii::$app->user->id])->one()) : ?>
                    <h2 class="usuario"><?= $usuario->log_us ?></h2><small class="small">Te sigue</small>
                <?php else : ?>
                    <h2 class="usuario"><?= $usuario->log_us ?></h2>
                <?php endif; ?>
                <?php if ($usuario->id != Yii::$app->user->id) : ?>
                    <?= Html::a($text, ['seguidores/follow', 'seguido_id' => $usuario->id], ['class' => 'follow follow-flex', 'id' => 'siguiendo']) ?>
                <?php else : ?>
                    <?= Html::a('Editar', ['usuarios/update', 'id' => Yii::$app->user->id], ['class' => 'follow follow-flex']) ?>
                    <?= ButtonDropdown::widget([
                        'options' => ['class' => 'delete'],
                        'direction' => 'left',
                        'label' => '···',
                        'dropdown' => [
                            'items' => [
                                Html::beginForm(['/usuarios/delete', 'id' => Yii::$app->user->id], 'post') . Html::img('icons/eliminar.png', ['id' => 'borrar-cuenta']) . Html::submitButton('Borrar cuenta', ['class' => 'eliminar-comentario']) . Html::endForm()
                            ],
                        ]
                    ]) ?>
                <?php endif; ?>
            </div>
        </div>
        <div class="row bio">
            <div class="col-2 d-flex justify-content-center">
                <img src="icons/bio.svg" id="bio">
            </div>
            <div class="col-8">
                <?= Html::tag('p', Html::encode($usuario->bio), ['class' => 'card-text']) ?>
            </div>
        </div>
        <div class="row location">
            <div class="col-2 d-flex justify-content-center">
                <img src="icons/location.svg" id="location">
            </div>
            <div class="col-8">
                <small><?= $usuario->ubi ?></small>
            </div>
        </div>
        <div class="row sg">
            <div class="col-3 d-flex justify-content-center">
                <a href="<?= Url::to(['seguidores/followers', 'id' => $usuario->id]); ?>">
                    <p id="sg"><?= Seguidores::find()->where(['seguido_id' => $usuario->id])->count() ?></p>
                </a>
            </div>
            <div class="col-3">
                <a href="<?= Url::to(['seguidores/followers', 'id' => $usuario->id]); ?>">
                    <h5>Seguidores</h5>
                </a>
            </div>
            <div class="col-3 d-flex justify-content-center">
                <a href="<?= Url::to(['seguidores/followers', 'id' => $usuario->id]); ?>">
                    <p><?= Seguidores::find()->where(['seguidor_id' => $usuario->id])->count() ?></p>
                </a>
            </div>
            <div class="col-3">
                <a href="<?= Url::to(['seguidores/followers', 'id' => $usuario->id]); ?>">
                    <h5>Seguidos</h5>
                </a>
            </div>
        </div>
        <?php if ($usuario->id != $actual->id) : ?>
            <?php if ($getRelacionados != null) : ?>
                <div class="row sg">
                    <div class="col-12 d-flex justify-content-center">
                        <a href="<?= Url::to(['usuarios/relacionados', 'id' => $usuario->id]); ?>">
                            <p class="s-relacionados">Hay cuentas que sigues que siguen a este usuario.</p>
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
        <div class="row pt">
            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <a class="nav-item nav-link active" id="nav-comments-tab" data-toggle="tab" href="#nav-comments" role="tab" aria-controls="nav-comments" aria-selected="true">Mis Comentarios</a>
                    <a class="nav-item nav-link" id="nav-likes-tab" data-toggle="tab" href="#nav-likes" role="tab" aria-controls="nav-likes" aria-selected="false">Likes</a>
                </div>
            </nav>
        </div>
        <div class="tab-content" id="nav-tabContent">
            <div class="tab-pane fade show active" id="nav-comments" role="tabpanel" aria-labelledby="nav-comments-tab">
                <?php \yii\widgets\Pjax::begin(['timeout' => 10000]) ?>
                <?php foreach ($comentarios as $comentario) : ?>
                    <?php
                    $user = Usuarios::findOne(['id' => $comentario->usuario_id]);
                    $likes = <<<EOT
                    var boton = $("#like$comentario->id");
                    boton.click(function(event) {
                        event.preventDefault();
                        $.ajax({
                            method: 'GET',
                            url: '$like',
                            data: {
                                'comentario_id': $comentario->id
                            },
                            success: function (data, code, jqXHR) {
                                var countlike$comentario->id = document.getElementById("countLike$comentario->id");
                                countlike$comentario->id.innerHTML = data[1];
                                if (data[0]) {
                                    document.getElementById("icon$comentario->id").src="icons/like.svg";
                                } else {
                                    document.getElementById("icon$comentario->id").src="icons/dislike.svg";
                                }
                            }
                        });
                    });
                    EOT;
                    $this->registerJs($likes);
                    $fav = <<<EOT
                    var boton = $("#save$comentario->id");
                    boton.click(function(event) {
                        event.preventDefault();
                        $.ajax({
                            method: 'GET',
                            url: '$save',
                            data: {
                                'comentario_id': $comentario->id
                            },
                            success: function (data, code, jqXHR) {
                                var text = '';
                                if (data[0])
                                    text = 'NotSave'
                                else
                                    text = 'Save'
                                var save$comentario->id = document.getElementById("save$comentario->id");
                                save$comentario->id.innerHTML = text;
                            }
                        });
                    });
                    EOT;
                    $this->registerJs($fav);
                    ?>
                    <div class="modal" id="respuesta<?= $comentario->id ?>">
                        <div class="modal-dialog">
                            <header class="modal-header">
                                <img src="<?= s3GetUrl($actual->url_img, 'ucomment') ?>" id="inicio">
                                <h4><?= $actual->log_us ?></h4>
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
                                                        <img src="<?= s3GetUrl($user->url_img, 'ucomment') ?>" id="citado">
                                                    </div>
                                                    <div class="col-10 d-flex justify-content-left">
                                                        <p class="center"><?= $user->log_us ?> · <?= $comentario->fecha($comentario->created_at) ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <p class="card-text"><?= $comentario->url($comentario->text); ?></p>
                                                <?php if ($comentario->url_img) : ?>
                                                    <div class="col-12 d-flex justify-content-right img">
                                                        <img src="<?= s3GetUrl($comentario->url_img, 'ucomment') ?>" alt="">
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="card" style="margin-top: 3%;">
                                            <div class="card-body">
                                                <?php $form = ActiveForm::begin(); ?>
                                                <?= $form->field($publicacion, 'text')->textarea(['maxlength' => true, 'placeholder' => 'Publica algo...',])->label(false) ?>
                                                <?= $form->field($publicacion, 'respuesta')->hiddenInput(['value' => $comentario->id])->label(false); ?>
                                                <?= $form->field($publicacion, 'url_img', ['options' => ['class' => '']])->fileInput()->label(false) ?>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12" style="margin-top: 3%;">
                                                <?= Html::submitButton('Publicar', ['class' => 'btn btn-primary']) ?>
                                                <?php ActiveForm::end(); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>
                    <div class="modal" id="citado<?= $comentario->id ?>">
                        <div class="modal-dialog">
                            <header class="modal-header">
                                <img src="<?= s3GetUrl($actual->url_img, 'ucomment') ?>" id="inicio">
                                <h4><?= $actual->log_us ?></h4>
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
                                        <?= $form->field($publicacion, 'text')->textarea(['maxlength' => true, 'placeholder' => 'Cita este comentario...',])->label(false) ?>
                                        <?= $form->field($publicacion, 'citado')->hiddenInput(['value' => $comentario->id])->label(false); ?>
                                        <?= $form->field($publicacion, 'url_img', ['options' => ['class' => '']])->fileInput()->label(false) ?>
                                        <div class="card" style="margin-top: 2%;">
                                            <div class="card-header">
                                                <div class="row">
                                                    <div class="col-2 d-flex justify-content-center">
                                                        <img src="<?= s3GetUrl($user->url_img, 'ucomment') ?>" id="citado">
                                                    </div>
                                                    <div class="col-10 d-flex justify-content-left">
                                                        <p class="center"><?= $user->log_us ?> · <?= $comentario->fecha($comentario->created_at) ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <p class="card-text"><?= $comentario->url($comentario->text); ?></p>
                                                <?php if ($comentario->url_img) : ?>
                                                    <div class="col-12 d-flex justify-content-right img">
                                                        <img src="<?= s3GetUrl($comentario->url_img, 'ucomment') ?>" alt="">
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div style="margin-top: 4%;">
                                            <?= Html::submitButton('Publicar', ['class' => 'btn btn-primary']) ?>
                                            <?php ActiveForm::end(); ?>
                                        </div>
                                    </div>
                                </div>

                            </section>
                        </div>
                    </div>
                    <div class="row com justify-content-center">
                        <div class="card">
                            <a href="<?= Url::to(['comentarios/view', 'id' => $comentario['id']]); ?>" id="comentario">
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col-1 d-flex justify-content-center">
                                            <img src="<?= s3GetUrl($user->url_img, 'ucomment') ?>" id="fcom">
                                        </div>
                                        <div class="col-6 d-flex justify-content-left">
                                            <p class="center"><?= $user->log_us ?> · <?= $comentario->fecha($comentario->created_at) ?></p>
                                        </div>
                                        <?php if ($comentario->usuario_id == Yii::$app->user->id) : ?>
                                            <div class="col-4 d-flex flex-row-reverse">
                                                <?= ButtonDropdown::widget([
                                                    'options' => ['class' => 'delete-comment'],
                                                    'direction' => 'left',
                                                    'label' => ' ···',
                                                    'dropdown' => [
                                                        'items' => [
                                                            Html::beginForm(['/comentarios/delete', 'id' => $comentario->id], 'post') . Html::img('icons/papelera.png', ['id' => 'papelera']) . Html::submitButton('Eliminar comentario', ['class' => 'eliminar-comentario']) . Html::endForm()
                                                        ],
                                                    ]
                                                ]) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <p class="card-text"><?= $comentario->url($comentario->text); ?></p>
                                    <?php if ($comentario->url_img) : ?>
                                        <div class="col-12 d-flex justify-content-right img">
                                            <img src="<?= s3GetUrl($comentario->url_img, 'ucomment') ?>" alt="">
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($comentario->citado) : ?>
                                        <?php $citado = Comentarios::find()->where(['id' => $comentario->citado])->one(); ?>
                                        <?php $uc = Usuarios::find()->where(['id' => $citado->usuario_id])->one(); ?>
                                        <div class="card" style="margin-top: 2%;">
                                            <div class="card-header">
                                                <div class="row">
                                                    <div class="col-2 d-flex justify-content-right">
                                                        <img src="<?= s3GetUrl($uc->url_img, 'ucomment') ?>" id="citado">
                                                    </div>
                                                    <div class="col-10 d-flex justify-content-left">
                                                        <a href="<?= Url::to(['usuarios/view', 'id' => $uc->id]); ?>">
                                                            <p class="center"><?= $uc->log_us ?> · <?= $citado->fecha($citado->created_at) ?></p>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                            <a href="<?= Url::to(['comentarios/view', 'id' => $citado->id]); ?>">
                                                <div class="card-body">
                                                    <p class="card-text"><?= $citado->url($citado->text); ?></p>
                                                    <?php if ($citado->url_img) : ?>
                                                        <div class="col-12 d-flex justify-content-right img">
                                                            <img src="<?= s3GetUrl($citado->url_img, 'ucomment') ?>" alt="">
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="card-footer">
                                    <div class="row">
                                        <div class="col-3 d-flex justify-content-center">
                                            <a class="open-modal" data-open="respuesta<?= $comentario->id ?>">
                                                <img src="icons/respuesta.svg" class="icon" id="answer">
                                            </a>
                                            <p class="count"><?= $comentario->getComentarios()->count(); ?></p>
                                        </div>
                                        <div class="col-3 d-flex justify-content-center">
                                            <a class="open-modal" data-open="citado<?= $comentario->id ?>">
                                                <img src="icons/citado.svg" class="icon" id="citar">
                                            </a>
                                            <p class="count"><?= $comentario->getCitados()->count(); ?></p>
                                        </div>
                                        <div class="col-3 d-flex justify-content-center">
                                            <a id="like<?= $comentario->id ?>" class="heart">
                                                <img src="<?= Likes::like($comentario->id) ? 'icons/like.svg' : 'icons/dislike.svg' ?>" class="icon" id="icon<?= $comentario->id ?>">
                                            </a>
                                            <p id="countLike<?= $comentario->id ?>" class="count"><?= Likes::find()->where(['comentario_id' => $comentario->id])->count() ?></p>
                                        </div>
                                        <div class="col-3">

                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php \yii\widgets\Pjax::end() ?>
                <div class="row com">
                    <div class="col-12 d-flex justify-content-center">
                        <?= LinkPager::widget([
                            'pagination' => $paginationC
                        ]); ?>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade shadow" id="nav-likes" role="tabpanel" aria-labelledby="nav-likes-tab">
                <?php \yii\widgets\Pjax::begin(['timeout' => 10000]) ?>
                <?php foreach ($ml as $coml) : ?>
                    <?php
                    $comentario = Comentarios::findOne(['id' => $coml->comentario_id]);
                    $user = Usuarios::findOne(['id' => $comentario->usuario_id]);
                    $likes = <<<EOT
                    var boton = $("#likepl$comentario->id");
                    boton.click(function(event) {
                        event.preventDefault();
                        $.ajax({
                            method: 'GET',
                            url: '$like',
                            data: {
                                'comentario_id': $comentario->id
                            },
                            success: function (data, code, jqXHR) {
                                var countlikepl$comentario->id = document.getElementById("countLikepl$comentario->id");
                                countlikepl$comentario->id.innerHTML = data[1];
                                if (data[0]) {
                                    document.getElementById("iconpl$comentario->id").src="icons/like.svg";
                                } else {
                                    document.getElementById("iconpl$comentario->id").src="icons/dislike.svg";
                                }
                            }
                        });
                    });
                    EOT;
                    $this->registerJs($likes);
                    $fav = <<<EOT
                    var boton = $("#save$comentario->id");
                    boton.click(function(event) {
                        event.preventDefault();
                        $.ajax({
                            method: 'GET',
                            url: '$save',
                            data: {
                                'comentario_id': $comentario->id
                            },
                            success: function (data, code, jqXHR) {
                                var text = '';
                                if (data[0])
                                    text = 'NotSave'
                                else
                                    text = 'Save'
                                var save$comentario->id = document.getElementById("save$comentario->id");
                                save$comentario->id.innerHTML = text;
                            }
                        });
                    });
                    EOT;
                    $this->registerJs($fav);
                    ?>
                    <div class="modal" id="respuestapl<?= $comentario->id ?>">
                        <div class="modal-dialog">
                            <header class="modal-header">
                                <img src="<?= s3GetUrl($actual->url_img, 'ucomment') ?>" id="inicio">
                                <h4><?= $actual->log_us ?></h4>
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
                                                        <img src="<?= s3GetUrl($user->url_img, 'ucomment') ?>" id="citado">
                                                    </div>
                                                    <div class="col-10 d-flex justify-content-left">
                                                        <p class="center"><?= $user->log_us ?> · <?= $comentario->fecha($comentario->created_at) ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <p class="card-text"><?= $comentario->url($comentario->text); ?></p>
                                                <?php if ($comentario->url_img) : ?>
                                                    <div class="col-12 d-flex justify-content-right img">
                                                        <img src="<?= s3GetUrl($comentario->url_img, 'ucomment') ?>" alt="">
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="card" style="margin-top: 3%;">
                                            <div class="card-body">
                                                <?php $form = ActiveForm::begin(); ?>
                                                <?= $form->field($publicacion, 'text')->textarea(['maxlength' => true, 'placeholder' => 'Publica algo...',])->label(false) ?>
                                                <?= $form->field($publicacion, 'respuesta')->hiddenInput(['value' => $comentario->id])->label(false); ?>
                                                <?= $form->field($publicacion, 'url_img', ['options' => ['class' => '']])->fileInput()->label(false) ?>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12" style="margin-top: 3%;">
                                                <?= Html::submitButton('Publicar', ['class' => 'btn btn-primary']) ?>
                                                <?php ActiveForm::end(); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>
                    <div class="modal" id="citadopl<?= $comentario->id ?>">
                        <div class="modal-dialog">
                            <header class="modal-header">
                                <img src="<?= s3GetUrl($actual->url_img, 'ucomment') ?>" id="inicio">
                                <h4><?= $actual->log_us ?></h4>
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
                                        <?= $form->field($publicacion, 'text')->textarea(['maxlength' => true, 'placeholder' => 'Cita este comentario...',])->label(false) ?>
                                        <?= $form->field($publicacion, 'citado')->hiddenInput(['value' => $comentario->id])->label(false); ?>
                                        <?= $form->field($publicacion, 'url_img', ['options' => ['class' => '']])->fileInput()->label(false) ?>
                                        <div class="card" style="margin-top: 2%;">
                                            <div class="card-header">
                                                <div class="row">
                                                    <div class="col-2 d-flex justify-content-center">
                                                        <img src="<?= s3GetUrl($user->url_img, 'ucomment') ?>" id="citado">
                                                    </div>
                                                    <div class="col-10 d-flex justify-content-left">
                                                        <p class="center"><?= $user->log_us ?> · <?= $comentario->fecha($comentario->created_at) ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <p class="card-text"><?= $comentario->url($comentario->text); ?></p>
                                                <?php if ($comentario->url_img) : ?>
                                                    <div class="col-12 d-flex justify-content-right img">
                                                        <img src="<?= s3GetUrl($comentario->url_img, 'ucomment') ?>" alt="">
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div style="margin-top: 4%;">
                                            <?= Html::submitButton('Publicar', ['class' => 'btn btn-primary']) ?>
                                            <?php ActiveForm::end(); ?>
                                        </div>
                                    </div>
                                </div>

                            </section>
                        </div>
                    </div>
                    <div class="row com justify-content-center">
                        <div class="card">
                            <a href="<?= Url::to(['comentarios/view', 'id' => $comentario['id']]); ?>" id="comentario">
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col-1 d-flex justify-content-center">
                                            <img src="<?= s3GetUrl($user->url_img, 'ucomment') ?>" id="fcom">
                                        </div>
                                        <div class="col-6 d-flex justify-content-left">
                                            <p class="center"><?= $user->log_us ?> · <?= $comentario->fecha($comentario->created_at) ?></p>
                                        </div>
                                        <?php if ($comentario->usuario_id == Yii::$app->user->id) : ?>
                                            <div class="col-4 d-flex flex-row-reverse">
                                                <?= ButtonDropdown::widget([
                                                    'options' => ['class' => 'delete-comment'],
                                                    'direction' => 'left',
                                                    'label' => ' ···',
                                                    'dropdown' => [
                                                        'items' => [
                                                            Html::beginForm(['/comentarios/delete', 'id' => $comentario->id], 'post') . Html::img('icons/papelera.png', ['id' => 'papelera']) . Html::submitButton('Eliminar comentario', ['class' => 'eliminar-comentario']) . Html::endForm()
                                                        ],
                                                    ]
                                                ]) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <p class="card-text"><?= $comentario->url($comentario->text); ?></p>
                                    <?php if ($comentario->url_img) : ?>
                                        <div class="col-12 d-flex justify-content-right img">
                                            <img src="<?= s3GetUrl($comentario->url_img, 'ucomment') ?>" alt="">
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($comentario->citado) : ?>
                                        <?php $citado = Comentarios::find()->where(['id' => $comentario->citado])->one(); ?>
                                        <?php $uc = Usuarios::find()->where(['id' => $citado->usuario_id])->one(); ?>
                                        <div class="card" style="margin-top: 2%;">
                                            <div class="card-header">
                                                <div class="row">
                                                    <div class="col-2 d-flex justify-content-right">
                                                        <img src="<?= s3GetUrl($uc->url_img, 'ucomment') ?>" id="citado">
                                                    </div>
                                                    <div class="col-10 d-flex justify-content-left">
                                                        <a href="<?= Url::to(['usuarios/view', 'id' => $uc->id]); ?>">
                                                            <p class="center"><?= $uc->log_us ?> · <?= $citado->fecha($citado->created_at) ?></p>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                            <a href="<?= Url::to(['comentarios/view', 'id' => $citado->id]); ?>">
                                                <div class="card-body">
                                                    <p class="card-text"><?= $citado->url($citado->text); ?></p>
                                                    <?php if ($citado->url_img) : ?>
                                                        <div class="col-12 d-flex justify-content-right img">
                                                            <img src="<?= s3GetUrl($citado->url_img, 'ucomment') ?>" alt="">
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="card-footer">
                                    <div class="row">
                                        <div class="col-3 d-flex justify-content-center">
                                            <a class="open-modal" data-open="respuestapl<?= $comentario->id ?>">
                                                <img src="icons/respuesta.svg" class="icon" id="answer">
                                            </a>
                                            <p class="count"><?= $comentario->getComentarios()->count(); ?></p>
                                        </div>
                                        <div class="col-3 d-flex justify-content-center">
                                            <a class="open-modal" data-open="citadopl<?= $comentario->id ?>">
                                                <img src="icons/citado.svg" class="icon" id="citar">
                                            </a>
                                            <p class="count"><?= $comentario->getCitados()->count(); ?></p>
                                        </div>
                                        <div class="col-3 d-flex justify-content-center">
                                            <a id="likepl<?= $comentario->id ?>" class="heart">
                                                <img src="<?= Likes::like($comentario->id) ? 'icons/like.svg' : 'icons/dislike.svg' ?>" class="icon" id="iconpl<?= $comentario->id ?>">
                                            </a>
                                            <p id="countLikepl<?= $comentario->id ?>" class="count"><?= Likes::find()->where(['comentario_id' => $comentario->id])->count() ?></p>
                                        </div>
                                        <div class="col-3">

                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php \yii\widgets\Pjax::end() ?>
                <div class="row com">
                    <div class="col-12 d-flex justify-content-center">
                        <?= LinkPager::widget([
                            'pagination' => $paginationL
                        ]); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-12 col-md-12 col-lg-4 b">
        <div class="card">
            <div class="card-header d-flex justify-content-center">
                <h5>Usuarios Sugeridos</h5>
            </div>
            <?php foreach ($sugeridos as $sugerido) : ?>
                <?php
                $textS = Seguidores::siguiendo($sugerido->id) ? 'Siguiendo' : 'Seguir';
                $jsS = <<<EOT
                var boton = $("#us$sugerido->id");
                boton.click(function(event) {
                    event.preventDefault();
                    $.ajax({
                        method: 'GET',
                        url: '$seguir',
                        data: {
                            'seguido_id': $sugerido->id
                        },
                        success: function (data, code, jqXHR) {
                            var text = ''
                            if (data[0])
                                text = 'Siguiendo'
                            else
                                text = 'Seguir'
                
                            var us$sugerido->id = document.getElementById("us$sugerido->id");
                            us$sugerido->id.innerHTML = text;
                            var sg = document.getElementById("sg");
                            sg.innerHTML = data[1];
                    }
                    });
                });
                EOT;
                $this->registerJs($jsS);
                ?>
                <div class="card-body s">
                    <div class="row">
                        <div class="col-2 d-flex justify-content-center">
                            <img src="<?= s3GetUrl($sugerido->url_img, 'ucomment') ?>" id="sugerido-img">
                        </div>
                        <div class="col-4 d-flex justify-content-left">
                            <a href="<?= Url::to(['usuarios/view', 'id' => $sugerido->id]); ?>">
                                <p class="center"><?= $sugerido->log_us ?></p>
                            </a>
                        </div>
                        <div class="col-2 d-flex flex-row">
                            <?= Html::a($textS, ['seguidores/follow', 'seguido_id' => $sugerido->id], ['class' => 'sbutton', 'id' => 'us' . $sugerido->id]) ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>