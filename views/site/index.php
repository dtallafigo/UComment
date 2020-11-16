<?php
use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;
/* @var $this yii\web\View */

$this->title = 'My Yii Application';
$js = <<<EOT
const openEls = document.querySelectorAll("[data-open]");
const closeEls = document.querySelectorAll("[data-close]");
const isVisible = "is-visible";

for (const el of openEls) {
  el.addEventListener("click", function() {
    const modalId = this.dataset.open;
    document.getElementById(modalId).classList.add(isVisible);
  });
}

for (const el of closeEls) {
  el.addEventListener("click", function() {
    this.parentElement.parentElement.parentElement.classList.remove(isVisible);
  });
}

document.addEventListener("click", e => {
  if (e.target == document.querySelector(".modal.is-visible")) {
    document.querySelector(".modal.is-visible").classList.remove(isVisible);
  }
});
EOT;
$this->registerJs($js);
?>


<div class="btn-group">
  <button type="button" class="open-modal" data-open="modal1">
    Launch first modal
  </button>
</div>

<div class="modal" id="modal1">
  <div class="modal-dialog">
    <header class="modal-header">
      <img src="<?= $model->url_img ?>" id="inicio">
      <h4><?= $model->log_us ?></h4>
    </header>
    <section class="modal-content">
        <?php $form = ActiveForm::begin(); ?>
            <?= $form->field($publicar, 'text')->textarea(['maxlength' => true, 'placeholder' => 'Publica algo...',]) ?>      
    </section>
    <footer class="modal-footer">
      <?= Html::submitButton('Publicar', ['class' => 'btn btn-primary']) ?>
        <?php ActiveForm::end(); ?>
    </footer>
  </div>
</div>