<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;
use app\models\Usuarios;
use yii\helpers\Url;
use app\models\Seguidores;


/* @var $this yii\web\View */
/* @var $model app\models\Seguidores */

$this->title = 'Seguidores de ' . $ua->log_us;
\yii\web\YiiAsset::register($this);
$seguir = Url::to(['seguidores/follow']);
?>
<div class="row g">
    <div class="col-sm-12 col-md-12 col-lg-12">
        <div class="row com">
            <div class="col-1">
                <a href="<?= Url::to(Yii::$app->request->referrer); ?>">
                    <img src="icons/hacia-atras.png" id="flecha">
                </a>
            </div>
            <div class="col-10">
                <h1><?= $ua->log_us ?></h1>
            </div>
        </div>
        <div class="row pt">
            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <a class="nav-item nav-link active" id="nav-seguidores-tab" data-toggle="tab" href="#nav-seguidores" role="tab" aria-controls="nav-seguidores" aria-selected="true">Seguidores</a>
                    <a class="nav-item nav-link" id="nav-seguidos-tab" data-toggle="tab" href="#nav-seguidos" role="tab" aria-controls="nav-seguidos" aria-selected="false">Seguidos</a>
                </div>
            </nav>
        </div>
        <div class="tab-content" id="nav-tabContent">
            <div class="tab-pane fade show active" id="nav-seguidores" role="tabpanel" aria-labelledby="nav-seguidores-tab">
                <?php foreach ($seguidores as $seguidor) : ?>
                    <?php $userR = Usuarios::findOne(['id' => $seguidor->seguidor_id]); ?>
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
                                        <img src="<?= $userR->url_img ?>" alt="" style="width: 90px; height: auto;">
                                    </div>
                                    <div class="col-sm-4 col-md-4 col-lg-6">
                                        <?php if (Seguidores::findOne(['seguido_id' => Yii::$app->user->id, 'seguidor_id' => $userR->id])) : ?>
                                            <small class="small-var">Te sigue</small>
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
            </div>
            <div class="tab-pane fade shadow" id="nav-seguidos" role="tabpanel" aria-labelledby="nav-seguidos-tab">
                <?php foreach ($seguidos as $seguido) : ?>
                    <?php $userS = Usuarios::findOne(['id' => $seguido->seguido_id]); ?>
                    <?php
                    $text = Seguidores::siguiendo($userS->id) ? 'Siguiendo' : 'Seguir';
                    $js1 = <<<EOT
                    var boton = $("#siguiendoPS$userS->id");
                    boton.click(function(event) {
                        event.preventDefault();
                        $.ajax({
                            method: 'GET',
                            url: '$seguir',
                            data: {
                                'seguido_id': $userS->id
                            },
                            success: function (data, code, jqXHR) {
                                var text = ''
                                if (data[0])
                                    text = 'Siguiendo'
                                else
                                    text = 'Seguir'
                    
                                var seguidoresPS$userS->id = document.getElementById("siguiendoPS$userS->id")
                                seguidoresPS$userS->id.innerHTML = text;
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
                                        <img src="<?= $userS->url_img ?>" alt="" style="width: 90px; height: auto;">
                                    </div>
                                    <div class="col-sm-4 col-md-4 col-lg-6">
                                        <?php if (Seguidores::findOne(['seguido_id' => Yii::$app->user->id, 'seguidor_id' => $userS->id])) : ?>
                                            <small>Te sigue</small>
                                        <?php else : ?>

                                        <?php endif; ?>
                                        <a href="<?= Url::to(['usuarios/view', 'id' => $userS->id]); ?>">
                                            <h4><?= $userS->log_us ?></h4>
                                        </a>
                                        <?= Html::tag('pre', Html::encode($userS->bio), ['class' => 'card-text']) ?>
                                    </div>
                                    <?php if ($userS->id != Yii::$app->user->id) : ?>
                                        <div class="col-sm-2 col-md-4 col-lg-2 d-flex justify-content-center">
                                            <?= Html::a($text, ['seguidores/follow', 'seguido_id' => $userS->id], ['class' => 'cbutton', 'id' => 'siguiendoPS' . $userS->id]) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>