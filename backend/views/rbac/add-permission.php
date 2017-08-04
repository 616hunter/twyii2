<?php
$form=\yii\bootstrap\ActiveForm::begin();
echo $form ->field($model,'name')->textInput(['readonly'=>$model->scenario!=\backend\models\PermissionForm::SCENARIO_ADD?true:false]);
echo $form->field($model,'description');
echo \yii\bootstrap\Html::submitButton('保存',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();