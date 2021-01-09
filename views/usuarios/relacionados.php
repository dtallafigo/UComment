<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;
use app\models\Usuarios;
use yii\helpers\Url;
use app\models\Seguidores;


/* @var $this yii\web\View */
/* @var $model app\models\Seguidores */

$this->title = 'Seguidores de relacionados';
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
    </div>
</div>