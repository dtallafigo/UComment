<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;
use app\models\Usuarios;
use yii\helpers\Url;
use app\models\Seguidores;

/* @var $this yii\web\View */
/* @var $model app\models\Likes */

$this->title = 'likes de ' . $ucl->log_us;
\yii\web\YiiAsset::register($this);
$seguir = Url::to(['seguidores/follow']);
?>
<div class="row g">
    <div class="col-12">
        <div class="row com">
            <div class="col-12">
                <h1>Likes de <?= $ucl->log_us ?></h1>
            </div>
        </div>
        <?php foreach ($likes as $like) : ?>
            <?php $user = Usuarios::findOne(['id' => $like->usuario_id]); ?>
            <?php
            $text = Seguidores::siguiendo($user->id) ? 'Siguiendo' : 'Seguir';
            $js1 = <<<EOT
            var boton = $("#siguiendo$user->id");
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
            
                        var seguidores$user->id = document.getElementById("siguiendo$user->id")
                        seguidores$user->id.innerHTML = text;
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
                            <div class="col-2 d-flex justify-content-center">
                                <img src="<?= s3GetUrl($user->url_img, 'ucomment') ?>" alt="" class="foto-header">
                            </div>
                            <div class="col-6">
                                <?php if (Seguidores::findOne(['seguido_id' => Yii::$app->user->id, 'seguidor_id' => $user->id])) : ?>
                                    <p>Te sigue</p>
                                <?php else : ?>

                                <?php endif; ?> 
                                <a href="<?= Url::to(['usuarios/view', 'id' => $user->id]); ?>">
                                    <h4><?= $user->log_us ?></h4>
                                </a>
                                <p class="card-text"><?= $user->url($user->bio) ?></p>
                            </div>
                            <?php if ($user->id != Yii::$app->user->id) : ?>
                                <div class="col-4 d-flex justify-content-center">
                                    <?= Html::a($text, ['seguidores/follow', 'seguido_id' => $user->id], ['class' => 'cbutton', 'id' => 'siguiendo' . $user->id]) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>