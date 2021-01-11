<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;
use app\models\Usuarios;
use yii\helpers\Url;
use app\models\Seguidores;


/* @var $this yii\web\View */
/* @var $model app\models\Seguidores */

$this->title = 'Seguidores relacionados';
\yii\web\YiiAsset::register($this);
$seguir = Url::to(['seguidores/follow']);
?>
<div class="row g">
    <div class="col-12">
        <div class="row com">
            <div class="col-1">
                <a href="<?= Url::to(Yii::$app->request->referrer); ?>">
                    <img src="icons/hacia-atras.png" id="flecha">
                </a>
            </div>
            <div class="col-10">
                <h1>Seguidores relacionados</h1>
            </div>
        </div>
        <?php if ($getUsuarios) : ?>
            <?php foreach ($getUsuarios as $userR) : ?>
                <?php
                $text = Seguidores::siguiendo($userR->id) ? 'Siguiendo' : 'Seguir';
                $js1 = <<<EOT
                    var boton = $("#siguiendoPR$userR->id");
                    boton.click(function(event) {
                        event.preventDefault();
                        $.ajax({
                            method: 'GET',
                            url: '$seguir',
                            data: {
                                'seguido_id': $userR->id
                            },
                            success: function (data, code, jqXHR) {
                                var text = ''
                                if (data[0])
                                    text = 'Siguiendo'
                                else
                                    text = 'Seguir'
                    
                                var seguidoresPR$userR->id = document.getElementById("siguiendoPR$userR->id")
                                seguidoresPR$userR->id.innerHTML = text;
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
                                    <img src="<?= s3GetUrl($userR->url_img, 'ucomment') ?>" alt="" class="foto-header">
                                </div>
                                <div class="col-sm-4 col-md-4 col-lg-6">
                                    <?php if (Seguidores::findOne(['seguido_id' => Yii::$app->user->id, 'seguidor_id' => $userR->id])) : ?>
                                        <small class="small-var">Te sigue</small>
                                    <?php else : ?>

                                    <?php endif; ?>
                                    <a href="<?= Url::to(['usuarios/view', 'id' => $userR->id]); ?>">
                                        <h4><?= $userR->log_us ?></h4>
                                    </a>
                                    <?= Html::tag('p', Html::encode($userR->bio), ['class' => 'card-text']) ?>
                                </div>
                                <?php if ($userR->id != Yii::$app->user->id) : ?>
                                    <div class="col-sm-2 col-md-4 col-lg-2 d-flex justify-content-center">
                                        <?= Html::a($text, ['seguidores/follow', 'seguido_id' => $userR->id], ['class' => 'cbutton', 'id' => 'siguiendoPR' . $userR->id]) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <div class="row com">
                <div class="col-12 d-flex justify-content-center">
                    <h3 class="s-relacionados">Aqui no hay nada que ver.</h3>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>