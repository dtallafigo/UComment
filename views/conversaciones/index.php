<?php

use app\assets\AppAsset;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

AppAsset::register($this);
$this->title = 'Conversaciones';
?>
<div class="row">
    <div class="col-12">
        <div class="row com">
            <div class="col-12">
                <h3>Chat</h3>
            </div>
            <div class="col-12 d-flex flex-row-reverse">
                <?= Html::a('Nuevo Mensaje', ['create'], ['class' => 'log-button']) ?>
            </div>
        </div>
        <div class="row com d-flex justify-content-center">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'layout' => "{items}\n{pager}",
                'columns' => [
                    [
                        'attribute' => 'receiver.username',
                        'label' => 'Conversacion con:',
                        'value' => function ($model, $key, $index, $widget) {
                            return $model->receiver->log_us;
                        }
                    ],
                    [
                        'attribute' => 'last.message',
                        'label' => 'Ultimo mensaje',
                        'value' => function ($model, $key, $index, $widget) {
                            $last = $model->getLast();
                            return $last->sender->log_us  . ": " .  Html::encode($last->cuerpo);
                        }
                    ],
                    [
                        'attribute' => 'last.momento',
                        'label' => 'Fecha',
                        'format' => 'DateTime',
                        'value' => function ($model, $key, $index, $widget) {
                            $last = $model->getLast();
                            return $last->created_at;
                        }
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{view}{delete}',
                    ]
                ]
            ]) ?>
        </div>
    </div>
</div>