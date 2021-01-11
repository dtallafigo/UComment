<?php

/* @var $this yii\web\View */

use yii\bootstrap4\Html;
use app\models\Seguidores;
use yii\helpers\Url;
use app\models\Usuarios;
use app\models\Likes;
use yii\bootstrap4\ActiveForm;
use app\models\Comentarios;

require '../web/uploads3.php';

$this->title = 'UComment: Busqueda';
$id = Yii::$app->user->id;
$seguir = Url::to(['seguidores/follow']);
$like = Url::to(['likes/like']);
$save = Url::to(['comsave/save']);
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
?>
<div class="row com g">

    <div class="col-12">
        <p>Busca por usuarios o comentario.</p>

        <p>
            <?= Html::beginForm(['site/busqueda'], 'get') ?>
            <div class="form-group">
                <?= Html::textInput('cadena', $cadena, ['class' => 'form-control']) ?>
            </div>
            <div class="form-group">
                <?= Html::submitButton('Buscar', ['class' => 'log-button']) ?>
            </div>
            <?= Html::endForm() ?>
        </p>
    </div>
</div>

<?php if ($countU > 0 || $countC > 0) : ?>
    <div class="row pt">
        <nav>
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                <a class="nav-item nav-link active" id="nav-usuarios-tab" data-toggle="tab" href="#nav-usuarios" role="tab" aria-controls="nav-usuarios" aria-selected="true">Usuarios</a>
                <a class="nav-item nav-link" id="nav-seguidos-tab" data-toggle="tab" href="#nav-seguidos" role="tab" aria-controls="nav-seguidos" aria-selected="false">Comentarios</a>
            </div>
        </nav>
    </div>
    <div class="tab-content" id="nav-tabContent">
        <div class="tab-pane fade show active" id="nav-usuarios" role="tabpanel" aria-labelledby="nav-usuarios-tab">
            <?php foreach ($usuarios as $user) : ?>
                <?php
                $text = Seguidores::siguiendo($user->id) ? 'Siguiendo' : 'Seguir';
                $js1 = <<<EOT
                    var boton = $("#siguiendoPR$user->id");
                    boton.click(function(event) {
                        event.preventDefault();
                        $.ajax({
                            method: 'GET',
                            url: '$seguir',
                            data: {
                                'seguido_id': $user->id
                            },
                            success: function (data, code, jqXHR) {
                                var text = ''
                                if (data[0])
                                    text = 'Siguiendo'
                                else
                                    text = 'Seguir'
                    
                                var seguidoresPR$user->id = document.getElementById("siguiendoPR$user->id")
                                seguidoresPR$user->id.innerHTML = text;
                        }
                        });
                    });
                    EOT;
                $this->registerJs($js1);
                ?>
                <div class="row com justify-content-center">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-sm-4 col-md-4 col-lg-4 d-flex justify-content-center">
                                    <img src="<?= s3GetUrl($user->url_img, 'ucomment') ?>" alt="" class="foto-header">
                                </div>
                                <div class="col-sm-4 col-md-4 col-lg-6">
                                    <?php if (Seguidores::findOne(['seguido_id' => Yii::$app->user->id, 'seguidor_id' => $user->id])) : ?>
                                        <small>Te sigue</small>
                                    <?php else : ?>

                                    <?php endif; ?>
                                    <a href="<?= Url::to(['usuarios/view', 'id' => $user->id]); ?>">
                                        <h4><?= $user->log_us ?></h4>
                                    </a>
                                    <p class="card-text"><?= $user->url($user->bio) ?></p>
                                </div>
                                <?php if ($user->id != Yii::$app->user->id) : ?>
                                    <div class="col-sm-2 col-md-4 col-lg-2 d-flex justify-content-center">
                                        <?= Html::a($text, ['seguidores/follow', 'seguido_id' => $user->id], ['class' => 'cbutton', 'id' => 'siguiendoPR' . $user->id]) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="tab-pane fade shadow" id="nav-seguidos" role="tabpanel" aria-labelledby="nav-seguidos-tab">
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
                                                    <p class="card-text"><?= $user->log_us ?> · <?= $comentario->fecha($comentario->created_at) ?></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <p class="card-text"><?= $comentario->url($comentario->text) ?></p>
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
                                            <?= $form->field($publicar, 'text')->textarea(['maxlength' => true, 'placeholder' => 'Publica algo...',])->label(false) ?>
                                            <?= $form->field($publicar, 'respuesta')->hiddenInput(['value' => $comentario->id])->label(false); ?>
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
                                    <?= $form->field($publicar, 'text')->textarea(['maxlength' => true, 'placeholder' => 'Cita este comentario...',])->label(false) ?>
                                    <?= $form->field($publicar, 'citado')->hiddenInput(['value' => $comentario->id])->label(false); ?>
                                    <div class="card" style="margin-top: 2%;">
                                        <div class="card-header">
                                            <div class="row">
                                                <div class="col-2 d-flex justify-content-center">
                                                    <img src="<?= s3GetUrl($user->url_img, 'ucomment') ?>" id="citado">
                                                </div>
                                                <div class="col-10 d-flex justify-content-left">
                                                    <p class="card-text"><?= $user->log_us ?> · <?= $comentario->fecha($comentario->created_at) ?></p>
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
                                    <div class="col-10 d-flex justify-content-left">
                                        <p class="card-text"><?= $user->log_us ?> · <?= $comentario->fecha($comentario->created_at) ?></p>
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
                                <?php if ($comentario->citado) : ?>
                                    <?php $citado = Comentarios::find()->where(['id' => $comentario->citado])->one(); ?>
                                    <?php $uc = Usuarios::find()->where(['id' => $citado->usuario_id])->one(); ?>
                                    <div class="card" style="margin-top: 2%;">
                                        <div class="card-header">
                                            <div class="row">
                                                <div class="col-2 d-flex justify-content-right">
                                                    <img src="<?= $uc->url_img ?>" id="citado">
                                                </div>
                                                <div class="col-10 d-flex justify-content-left">
                                                    <a href="<?= Url::to(['usuarios/view', 'id' => $uc->id]); ?>">
                                                        <p class="card-text"><?= $uc->log_us ?> · <?= $citado->fecha($citado->created_at) ?></p>
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
        </div>
    </div>
<?php endif; ?>