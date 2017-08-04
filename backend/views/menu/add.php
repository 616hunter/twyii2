<?php
$form=\yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'name');
echo $form->field($model,'permissions')->dropDownList(
    array_merge(['顶级'=>'顶级'],
    \yii\helpers\ArrayHelper::map(\yii::$app->authManager->getPermissions(),'name','name'))
);
echo $form->field($model,'menu')->dropDownList(\yii\helpers\ArrayHelper::map(\backend\models\Menu::find()->where('parent_id<=1')->all(),'id','name'));
echo $form->field($model,'sort')->textInput(['type'=>'number']);
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();