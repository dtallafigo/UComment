<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ListView;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\models\Group */

$js = <<<EOT
window.setInterval(()=>{
    $.pjax.reload({container: '#pjax', async: true});
}, 5000);
EOT;
$this->registerJs($js);
$templateMessage = '{label}<div class="input-group">{input}
<span class="input-group-btn">
    <button type="submit" class="btn btn-primary" name="sender">Enviar</button>
</span></div>{hint}{error}';

?>
<div class="row">
    <div class="col-12">
        <div class="row com">
            <div class="col-12">
                <h3>Mensajes con <?= $receiver->log_us  ?></h3>
            </div>
        </div>
        <div class="row com">
            <div class="col-12">
                <?php Pjax::begin(['id' => 'pjax']); ?>
                <div class="list" id="list">
                    <?= ListView::widget([
                        'dataProvider' => $dataProvider,
                        'itemOptions' => ['class' => 'item'],
                        'options' => [
                            'tag' => 'div',
                            'class' => 'message-wrapper',
                            'id' => 'message-wrapper',
                        ],
                        'layout' => "{items}\n{pager}",
                        'itemView' => '../mensajes/_view.php',
                    ]) ?>
                </div>
                <?php Pjax::end(); ?>
            </div>
        </div>
        <div class="row com">
            <div class="col-12">
                <div class="message-form">

                    <?php $form = ActiveForm::begin(); ?>

                    <?= $form->field($mensaje, 'cuerpo', ['template' => $templateMessage, 'inputOptions' => [
                        'autocomplete' => 'off', 'class' => 'form-control'
                    ]])->textInput()->label(false) ?>

                    <?php ActiveForm::end(); ?>

                </div>
            </div>
        </div>
    </div>
</div>