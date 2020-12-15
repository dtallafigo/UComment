<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;
use app\models\Usuarios;
use app\models\Comentarios;
use yii\helpers\Url;
use app\models\Likes;
use yii\bootstrap4\ActiveForm;


/* @var $this yii\web\View */
/* @var $model app\models\Comentarios */

$this->title = $model->id;
\yii\web\YiiAsset::register($this);
$userA = Usuarios::findOne(['id' => $model->usuario_id]);
$userB = Usuarios::findOne(['id' => Yii::$app->user->id]);
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
$user = Usuarios::findOne(['id' => $model->usuario_id]);
$likes = <<<EOT
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
$this->registerJs($likes);
?>
<div class="row">
    <div class="col-9" style="background-color: white;">
        <div class="modal" id="respuesta<?= $model->id ?>">
            <div class="modal-dialog">
                <header class="modal-header">
                    <img src="<?= $userB->url_img ?>" id="inicio">
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
                                            <img src="<?= $userB->url_img ?>" id="citado">
                                        </div>
                                        <div class="col-4 d-flex justify-content-left">
                                            <p class="card-title"><?= $userB->log_us ?></p>
                                        </div>
                                        <div class="col-6 d-flex justify-content-center">
                                            <p><?= $model->fecha($model->created_at) ?></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <p class="card-text"><?= $model->text ?></p>
                                </div>
                            </div>
                            <div class="card" style="margin-top: 3%;">
                                <div class="card-body">
                                    <?php $form = ActiveForm::begin(); ?>
                                    <?= $form->field($publicacion, 'text')->textarea(['maxlength' => true, 'placeholder' => 'Publica algo...',])->label(false) ?>
                                    <?= $form->field($publicacion, 'respuesta')->hiddenInput(['value' => $model->id])->label(false); ?>
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
        <div class="modal" id="citado<?= $model->id ?>">
            <div class="modal-dialog">
                <header class="modal-header">
                    <img src="<?= $userB->url_img ?>" id="inicio">
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
                            <?= $form->field($publicacion, 'text')->textarea(['maxlength' => true, 'placeholder' => 'Cita este comentario...',])->label(false) ?>
                            <?= $form->field($publicacion, 'citado')->hiddenInput(['value' => $model->id])->label(false); ?>
                            <div class="card" style="margin-top: 2%;">
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col-2 d-flex justify-content-center">
                                            <img src="<?= $userB->url_img ?>" id="citado">
                                        </div>
                                        <div class="col-4 d-flex justify-content-left">
                                            <p class="card-title"><?= $userB->log_us ?></p>
                                        </div>
                                        <div class="col-6 d-flex justify-content-center">
                                            <p><?= $model->fecha($model->created_at) ?></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <p class="card-text"><?= $model->text ?></p>
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
                <div class="card-body">
                    <div class="row">
                        <div class="col-2">
                            <img src="<?= $userA->url_img ?>" alt="" style="width: 50px; height: auto;">
                        </div>
                        <div class="col-6 justify-content-left">
                            <h3><?= $userA->log_us ?></h3>
                        </div>
                        <div class="col-4">
                            <p><?= $model->fecha($model->created_at) ?></p>
                        </div>
                    </div>
                    <div class="col-12" style="margin-top: 3%;">
                        <p><?= $model->text ?></p>
                    </div>
                </div>
                <?php if ($model->citado) : ?>
                    <?php $citado = Comentarios::find()->where(['id' => $model->citado])->one(); ?>
                    <?php $uc = Usuarios::find()->where(['id' => $citado->usuario_id])->one(); ?>
                    <div class="card-body">
                        <a href="<?= Url::to(['comentarios/view', 'id' => $citado->id]); ?>" id="comentario">
                            <div class="card" style="margin-bottom: 2%;">
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col-2 d-flex justify-content-center">
                                            <img src="<?= $uc->url_img ?>" id="citado">
                                        </div>
                                        <div class="col-4 d-flex justify-content-left">
                                            <p class="card-title"><?= $uc->log_us ?></p>
                                        </div>
                                        <div class="col-6 d-flex justify-content-center">
                                            <p><?= $citado->fecha($citado->created_at) ?></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <p class="card-text"><?= $citado->text ?></p>
                                </div>
                            </div>
                        </a>
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
                            <p id="countLike<?= $model->id ?>" class="count"><?= Likes::find()->where(['comentario_id' => $model->id])->count() ?> Likes</p>
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
</div>