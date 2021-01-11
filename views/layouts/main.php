<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap4\Nav;
use yii\bootstrap4\NavBar;
use yii\bootstrap4\Breadcrumbs;
use app\assets\AppAsset;
use app\models\Usuarios;

AppAsset::register($this);
$js2 = <<<EOT
$(document).ready(function(){ //Hacia arriba
    irArriba();
  });
  
  function irArriba(){
    $('.ir-arriba').click(function(){ $('body,html').animate({ scrollTop:'0px' },1000); });
    $(window).scroll(function(){
      if($(this).scrollTop() > 0){ $('.ir-arriba').slideDown(600); }else{ $('.ir-arriba').slideUp(600); }
    });
    $('.ir-abajo').click(function(){ $('body,html').animate({ scrollTop:'1000px' },1000); });
  }
EOT;
$this->registerJs($js2);
?>
<?php $this->beginPage() ?>

<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">

<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <?php $log = Usuarios::findOne(['id' => Yii::$app->user->id]) ?>
</head>

<body>
    <?php $this->beginBody() ?>

    <a class="ir-arriba" javascript:void(0) title="Volver arriba">
        <span class="fa-stack">
            <i class="fa fa-circle fa-stack-2x"></i>
            <i class="fa fa-arrow-up fa-stack-1x fa-inverse"></i>
        </span>
    </a>

    <div class="wrap">
        <?php
        NavBar::begin([
            'brandLabel' => Html::img('@web/icons/logodavo2.png', ['alt' => Yii::$app->name, 'id' => 'logo']),
            'brandUrl' => Yii::$app->homeUrl,
            'options' => [
                'class' => 'navbar-dark bg-dark navbar-expand-md fixed-top',
            ],
            'collapseOptions' => [
                'class' => 'justify-content-end',
            ],
        ]);
        echo Nav::widget([
            'options' => ['class' => 'navbar-nav'],
            'items' => [
                [
                    'label' => 'Inicio',
                    'url' => ['/site/index']
                ],
                [
                    'label' => 'Busqueda',
                    'url' => ['/site/busqueda']
                ],
                [
                    'label' => 'Perfil',
                    'url' => ['/usuarios/view', 'id' => Yii::$app->user->id]
                ],
                [
                    'label' => 'Usuarios',
                    'url' => ['/usuarios/index'],
                    'visible' => Yii::$app->user->isGuest ? false : (Yii::$app->user->id == 1)
                ],
                [
                    'label' => 'Comentarios',
                    'url' => ['/comentarios/index'],
                    'visible' => Yii::$app->user->isGuest ? false : (Yii::$app->user->id == 1)
                ],
                [
                    'label' => Yii::$app->user->isGuest ? 'Usuarios' : Yii::$app->user->identity->log_us,
                    'items' => [
                        Yii::$app->user->isGuest ? ([
                                'label' => 'Login',
                                'url' => ['/site/login']
                            ]) : (Html::beginForm(['/site/logout'], 'post')
                            . Html::submitButton(
                                'Logout (' . Yii::$app->user->identity->log_us . ')',
                                ['class' => 'dropdown-item'],
                            )
                            . Html::endForm()),
                    ],
                ],
            ]
        ]);
        NavBar::end();
        ?>

        <div class="container">
            <?= Breadcrumbs::widget([
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ]) ?>
            <?= Alert::widget() ?>
            <?= $content ?>
            <?php
            if (!Yii::$app->getRequest()->getCookies()->getValue('aceptar')) {
                Yii::$app->session->setFlash('info', 'Este sitio usa cookies, pulsa en el boton para confirmar que aceptas el uso de cookies' . Html::a('Aceptar', ['site/cookie'], ['class' => 'btn btn-outline-info']));
            }
            ?>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <p class="float-left">UComment</p>

            <p class="float-right"><?= Yii::powered() ?></p>
        </div>
    </footer>

    <?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage() ?>