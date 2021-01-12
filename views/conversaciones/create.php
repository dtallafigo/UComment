<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $model app\models\Group */

$url = Url::to(['/conversaciones/buscar-usuarios']);
$js = <<<EOT
    $('#conversacion-username').on('focus keyup', function () {
        $.ajax({
            method: 'get',
            url: '$url',
            data: {
                name: $('#conversacion-username').val()
            },
            success: function (data, status, event) {
                var usuarios = JSON.parse(data);
                console.log(usuarios);
                $('#conversacion-username').autocomplete({source: usuarios});
            }
        });
    });
EOT;
$this->registerJs($js);
$this->title = 'Buscar Usuarios';
?>
<div class="row">
    <div class="col-12">
        <div class="row com">
            <div class="col-12">
                <h1><?= Html::encode($this->title) ?></h1>
            </div>
        </div>
        <div class="row com">
            <div class="col-12">
                <?php $form = ActiveForm::begin(); ?>

                <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>

                <div class="form-group">
                    <?= Html::submitButton('Enviar mensaje', ['class' => 'log-button']) ?>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>