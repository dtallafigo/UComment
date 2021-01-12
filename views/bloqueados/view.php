<?php

use yii\bootstrap4\Html;
use yii\helpers\Url;
use yii\bootstrap4\ButtonDropdown;
use app\models\Seguidores;

$this->title = $model->log_us;
\yii\web\YiiAsset::register($this);
$seguir = Url::to(['seguidores/follow']);
?>
<div class="row">
    <div class="col-sm-12 col-md-12 col-lg-8">
        <div class="row com">
            <div class="col-1">
                <a href="<?= Url::to(Yii::$app->request->referrer); ?>">
                    <img src="icons/hacia-atras.png" id="flecha">
                </a>
            </div>
            <div class="col-10 d-flex justify-content-left">
                <h4><?= $model->log_us ?></h4>
            </div>
        </div>
        <div class="row user">
            <div class="col-sm-12 col-md-4 col-lg-4 d-flex justify-content-center align-self-center text-center">
                <img src="<?= s3GetUrl($model->url_img, 'ucomment') ?>" id="perfil">
            </div>
            <div class="col-sm-12 col-md-8 col-lg-8">
                <h2 class="usuario"><?= $model->log_us ?></h2>
                <?php if ($bloquear1) : ?>
                    <a href="" class="follow follow-flex">Bloqueado</a>
                    <?= ButtonDropdown::widget([
                        'options' => ['class' => 'delete'],
                        'direction' => 'left',
                        'label' => '···',
                        'dropdown' => [
                            'items' => [
                                Html::beginForm(['/bloqueados/bloquear', 'id' => $model->id], 'post') . Html::img('icons/eliminar.png', ['id' => 'borrar-cuenta']) . Html::submitButton('Desbloquear', ['class' => 'eliminar-comentario']) . Html::endForm()
                            ],
                        ]
                    ]) ?>
                <?php else : ?>
                    <button class="follow follow-flex" disabled="disabled">Bloqueado</button>
                <?php endif; ?>
            </div>
        </div>
        <div class="row bio">
            <div class="col-2 d-flex justify-content-center">
                <img src="icons/bio.svg" id="bio">
            </div>
            <div class="col-8">
                <p class="card-text"><?= $model->url($model->bio) ?></p>
            </div>
        </div>
        <div class="row location">
            <div class="col-2 d-flex justify-content-center">
                <img src="icons/location.svg" id="location">
            </div>
            <div class="col-8">
                <small><?= $model->url($model->ubi) ?></small>
            </div>
        </div>
        <div class="row com">
            <?php if ($bloquear1) : ?>
                <div class="col-12 d-flex justify-content-center">
                    <h3>Has bloqueado a este usuario.</h3>
                </div>
            <?php else : ?>
                <div class="col-12 d-flex justify-content-center">
                    <h3>Este usuario te ha bloqueado.</h3>
                </div>
            <?php endif; ?>
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