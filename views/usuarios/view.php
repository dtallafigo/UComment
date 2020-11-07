<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;
use app\controllers\SeguidoresController;
use app\models\Seguidores;

/* @var $this yii\web\View */
/* @var $model app\models\Usuarios */

$this->title = 'Perfil de ' . $usuario->log_us;
$url = Url::to(['seguidores/follow']);
$js = <<<EOT
var boton = $("#siguiendo");
boton.click(function(event) {
    event.preventDefault();
    $.ajax({
        method: 'GET',
        url: '$url',
        data: {
            'seguido_id': $usuario->id
        },
        success: function (data, code, jqXHR) {
            var text

            if (data[0])
                text = 'Dejar de seguir'
                
            else
                text = 'Seguir'

            var seguidores = document.getElementById('siguiendo')
            seguidores.innerHTML = text
    }
    });
});
EOT;
$this->registerJs($js);
Yii::$app->formatter->locale = 'ES';
$text = Seguidores::siguiendo($usuario->id) ? 'Dejar de seguir' : 'Seguir';
$class = Seguidores::siguiendo($usuario->id) ? 'btn btn-danger' : 'btn btn-primary';
?>

<div class="container">
    <div class="row">
        <div class="col-9">
            <div class="row">
                <div class="col-3">
                    <img src="<?= $usuario->url_img ?>" id="perfil">
                </div>
                <div class="col-9">
                    <h3 class="usuario"><?= $usuario->log_us ?></h3>
                    <?= Html::a($text, ['seguidores/follow', 'seguido_id' => $usuario->id], ['class' => 'btn btn-outline-info', 'id' => 'siguiendo']) ?>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    
                </div>
            </div>
        </div>
        <div class="col-3">
            <h1>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Libero, eius neque. Quidem corporis id, architecto placeat corrupti maiores deserunt distinctio numquam culpa dicta quod facere et modi perspiciatis voluptas deleniti?</h1>
        </div>
    </div>
</div>