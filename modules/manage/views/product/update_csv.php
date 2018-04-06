<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = "Upload Products";

$this->registerJs('$("#product-category_id").change(function(){
var val = $(this).val();
$.get("/site/get?alias=subcat&cat="+val, function(data) {
    console.log(data);
    $("#product-sub_category_id").html(data);
});
});');
?>

    <?php ActiveForm::begin(['options' => ['enctype'=>'multipart/form-data'], 'layout'=>'horizontal']); ?>
    
    
            <div class="form-group">
                <div class="col-xs-12">
                    <label>Select a csv file</label>
                    <?= Html::fileInput('csv', NULL, ['accept'=>'.csv']) ?>
                </div>
            </div>
        <?= Html::submitButton('Upload', ['class' => 'btn btn-primary']) ?>
    <?php ActiveForm::end(); ?>
