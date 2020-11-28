<?php

use yii\bootstrap4\Html;
use yii\helpers\Url;
use yii\bootstrap4\ActiveForm;
use app\models\Seguidores;
use app\models\Usuarios;
use app\models\Likes;

/* @var $this yii\web\View */
/* @var $model app\models\Usuarios */

$this->title = 'Perfil de ' . $usuario->log_us;
$text = Seguidores::siguiendo($usuario->id) ? 'Dejar de seguir' : 'Seguir';
$seguir = Url::to(['seguidores/follow']);
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
                text = 'Dejar de seguir'
            else
                text = 'Seguir'

            var seguidores = document.getElementById("siguiendo")
            var countLikes = document.getElementById("sg")
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
Yii::$app->formatter->locale = 'ES';
?>

<div class="container">
    <div class="row" style="margin: 0 2% 0 2%;">
        <div class="col-xl-9 col-md-12" style="border: 1px solid;">
            <div class="row user">
                <div class="col-xl-3">
                    <img src="<?= $usuario->url_img ?>" id="perfil">
                </div>
                <div class="col-xl-9">
                    <h2 class="usuario"><?= $usuario->log_us ?></h2>
                    <?= Html::a($text, ['seguidores/follow', 'seguido_id' => $usuario->id], ['class' => 'follow', 'id' => 'siguiendo']) ?>
                </div>
            </div>
            <div class="row bio">
                <div class="col-1 d-flex justify-content-center" style="text-align: center;">
                    <img src="icons/bio.svg" id="bio">
                </div>
                <div class="col-11">
                    <p><?= $usuario->bio ?></p>
                </div>
            </div>
            <div class="row location">
                <div class="col-1 d-flex justify-content-center">
                    <img src="icons/location.svg">
                </div>
                <div class="col-11">
                    <p><?= $usuario->ubi ?></p>
                </div>
            </div>
            <div class="row sg">
                <div class="col-3 d-flex justify-content-center">
                    <p id="sg"><?= Seguidores::find()->where(['seguido_id' => $usuario->id])->count() ?></p>
                </div>
                <div class="col-3">
                    <h5>Seguidores</h5>
                </div>
                <div class="col-3 d-flex justify-content-center">
                    <p><?= Seguidores::find()->where(['seguidor_id' => $usuario->id])->count() ?></p>
                </div>
                <div class="col-3">
                    <h5>Seguidos</h5>
                </div>
            </div>
                <?php foreach ($comentarios as $comentario) : ?>
                <?php 
                $user = Usuarios::findOne(['id' => $comentario->usuario_id]);
                $js3 = <<<EOT
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
                            var text = '';

                            if (data[0])
                                text = 'Dislike'
                            else
                                text = 'Like'
                            var like$comentario->id = document.getElementById("like$comentario->id");
                            var countlike$comentario->id = document.getElementById("countLike$comentario->id");
                            countlike$comentario->id.innerHTML = data[1];
                            like$comentario->id.innerHTML = text;
                        }
                    });
                });
                EOT;
                $this->registerJs($js3);
                $js4 = <<<EOT
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
                $this->registerJs($js4);  
                ?>
                    <div class="row com justify-content-center">
                        <div class="card">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-1 d-flex justify-content-center">
                                        <img src="<?= $user->url_img ?>" id="fcom">
                                    </div>
                                    <div class="col-5 d-flex justify-content-left">
                                        <p class="card-title"><?= $user->log_us ?></p>
                                    </div>
                                    <div class="col-6 d-flex justify-content-center">
                                        <p><?= $comentario->created_at ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <p class="card-text"><?= $comentario->text ?></p>
                            </div>
                            <div class="card-footer">
                                <div class="btn-group">
                                    <button type="button" class="open-modal" data-open="respuesta<?=$comentario->id?>">
                                        <img src="icons/respuesta.ico" style="width: 20%; height: auto;">
                                    </button>
                                </div>
                                <div class="btn-group">
                                    <button type="button" class="open-modal" data-open="citado<?=$comentario->id?>">
                                    Citado
                                    </button>
                                </div>
                                <div class="btn-group">
                                    <button id="like<?= $comentario->id ?>">like</button>
                                    <p id="countLike<?= $comentario->id ?>"><?= Likes::find()->where(['comentario_id' => $comentario->id])->count() ?></p>
                                </div>
                                <div class="btn-group">
                                    <button id="save<?= $comentario->id ?>">save</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal" id="respuesta<?=$comentario->id?>">
                        <div class="modal-dialog">
                            <header class="modal-header">
                                <img src="<?= $usuario->url_img ?>" id="inicio">
                                <h4><?= $usuario->log_us ?></h4>
                                <button class="close-modal" aria-label="close modal" data-close>
                                ✕  
                                </button>
                            </header>
                            <section class="modal-content">
                                <?php $form = ActiveForm::begin(); ?>
                                <?= $form->field($publicacion, 'text')->textarea(['maxlength' => true, 'placeholder' => 'Publica algo...',]) ?>
                                <?= $form->field($publicacion, 'respuesta')->hiddenInput(['value' => $comentario->id])->label(false); ?>
                                <?= Html::submitButton('Publicar', ['class' => 'btn btn-primary']) ?>
                                <?php ActiveForm::end(); ?>
                            </section>
                        </div>
                    </div>
                    <div class="modal" id="citado<?=$comentario->id?>">
                        <div class="modal-dialog">
                            <header class="modal-header">
                                <img src="<?= $usuario->url_img ?>" id="inicio">
                                <h4><?= $usuario->log_us ?></h4>
                                <button class="close-modal" aria-label="close modal" data-close>
                                ✕  
                                </button>
                            </header>
                            <section class="modal-content">
                                <?php $form = ActiveForm::begin(); ?>
                                <?= $form->field($publicacion, 'text')->textarea(['maxlength' => true, 'placeholder' => 'Publica algo...',]) ?>
                                <?= $form->field($publicacion, 'citado')->hiddenInput(['value' => $comentario->id])->label(false); ?>
                                <?= Html::submitButton('Publicar', ['class' => 'btn btn-primary']) ?>
                                <?php ActiveForm::end(); ?>
                                <div class="card" style="margin-top: 2%;">
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col-1 d-flex justify-content-center">
                                            <img src="<?= $user->url_img ?>" id="citado">
                                        </div>
                                        <div class="col-5 d-flex justify-content-left">
                                            <p class="card-title"><?= $user->log_us ?></p>
                                        </div>
                                        <div class="col-6 d-flex justify-content-center">
                                            <p><?= $comentario->created_at ?></p>
                                        </div>
                                    </div>
                                </div>
                                    <div class="card-body">
                                        <p class="card-text"><?= $comentario->text ?></p>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>
                <?php endforeach; ?>
        </div>
        <div class="col-xl-3">
                
        </div>
    </div>
</div>