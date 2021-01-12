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
use app\models\Comsave;

$this->title = 'UComment';
$seguir = Url::to(['seguidores/follow']);
$js = <<<EOT
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
$this->registerJs($js);
$like = Url::to(['likes/like']);
$save = Url::to(['comsave/save']);
?>
<div class="row">
	<div class="col-sm-12 col-md-12 col-lg-8">
		<div class="row com">
			<div class="col-12">
				<h1>Inicio</h1>
			</div>
		</div>
		<div class="row com">
			<div class="card">
				<div class="card-header">
					<div class="row">
						<div class="col-2 d-flex justify-content-center">
							<img src="<?= s3GetUrl($usuario->url_img, 'ucomment') ?>" id="fcom">
						</div>
						<div class="col-10 d-flex justify-content-left">
							<p class="card-text"><?= $usuario->log_us ?></p>
						</div>
					</div>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-12">
							<?php $form = ActiveForm::begin(); ?>
							<?= $form->field($publicacion, 'text')->textarea(['maxlength' => true, 'placeholder' => 'Publica algo...',])->label(false) ?>
							<?= $form->field($publicacion, 'url_img', ['options' => ['class' => '']])->fileInput()->label(false) ?>
						</div>
						<div class="col-12 d-flex flex-row-reverse">
							<?= Html::submitButton('Publicar', ['class' => 'log-button']) ?>
							<?php ActiveForm::end(); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
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
						if (data[0]) {
							document.getElementById("fav$comentario->id").src="icons/save.png";
						} else {
							document.getElementById("fav$comentario->id").src="icons/not-save.png";
						}
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
					<a href="<?= Url::to(['comentarios/view', 'id' => $comentario['id']]); ?>" id="comentario">
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
					</a>
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
							<div class="col-3 d-flex justify-content-center">
								<a id="save<?= $comentario->id ?>" class="heart">
									<img src="<?= Comsave::fav($comentario->id) ? 'icons/save.png' : 'icons/not-save.png' ?>" class="icon" id="fav<?= $comentario->id ?>">
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
		<?php \yii\widgets\Pjax::end() ?>
		<div class="row com">
			<div class="col-12 d-flex justify-content-center">
				<?= LinkPager::widget([
					'pagination' => $pagination
				]); ?>
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