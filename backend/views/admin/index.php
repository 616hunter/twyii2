<?php
/* @var $this yii\web\View */
?>
<table class="table table-hover table-bordered table-striped table-responsive table-condensed">
    <tr class="danger text text-center">
    <th class="text- center">ID</th>
    <th class="text-center">用户名</th>
    <th class="text-center">邮箱</th>
    <th class="text-center">最后登录事件</th>
    <th class="text-center">最后登录ip</th>
    <th class="text-center">操作</th>
    </tr>
    <?php foreach($models as $model):?>
        <tr>
            <td class="text-center"><?=$model->id?></td class="text-center">
            <td class="text-center"><?=$model->username?></td class="text-center">
            <td class="text-center"><?=$model->email?></td class="text-center">
            <td class="text-center"><?=date('Y-m-d H:i:s',$model->last_login_time)?></td class="text-center">
            <td class="text-center"><?=$model->last_login_ip?></td class="text-center">
            <td class="text-center">
                <?=\yii::$app->user->can('admin/edit')?\yii\bootstrap\Html::a('修改',['admin/edit','id'=>$model->id],['class'=>'glyphicon glyphicon-pencil btn btn-info btn-xs']):''?>
                <?=\yii::$app->user->can('admin/delete')?\yii\bootstrap\Html::a('删除',['admin/delete','id'=>$model->id],['class'=>'glyphicon glyphicon-trash btn btn-danger btn-xs']):''?>
            </td class="text-center">
        </tr>
    <?php endforeach;?>

</table>
