<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;
use app\models\Seguidores;
use yii\helpers\Url;

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
            var texto= data[0]?"Dejar de seguir":"Seguir"
            boton.toggle("slide",1000);
            setTimeout( ()=> {
                boton.html(texto);
            }, 1000);
            boton.toggle("slide",1000);
            var seguidores = document.getElementById('seguidores')
            seguidores.innerHTML = data[1]
    }
    });
});
EOT;
$this->registerJs($js);
Yii::$app->formatter->locale = 'ES';
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
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <?= Html::a(['seguidores/siguiendo', 'seguido_id', $usuario->id] ? 'Dejar de seguir' : 'Seguir', ['seguidores/follow', 'seguido_id' => $usuario->id], ['class' => 'btn btn-success text-light', 'id' => 'siguiendo']) ?>
                </div>
            </div>
        </div>
        <div class="col-3">
            <h1>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Libero, eius neque. Quidem corporis id, architecto placeat corrupti maiores deserunt distinctio numquam culpa dicta quod facere et modi perspiciatis voluptas deleniti?</h1>
        </div>
    </div>
</div>