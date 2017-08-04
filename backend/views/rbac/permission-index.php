<?=\yii\bootstrap\Html::a('添加',['add-permission'],['class'=>'btn btn-success'])?>
<table class="table table-hover table-condensed table-responsive table-bordered table-view table-striped">
    <tr>
        <td>名称</td>
        <td>简介</td>
        <td>操作</td>
    </tr>
<?php foreach($models as $model):?>
    <tr>
        <td><?=$model->name?></td>
        <td><?=$model->description?></td>
        <td>
            <?=\yii::$app->user->can('rbac/permission-edit')?\yii\bootstrap\Html::a('修改',['permission-edit','name'=>$model->name],['class'=>'btn btn-info btn-sm']):''?>
            <?=\yii::$app->user->can('rbac/permission-delete')?\yii\bootstrap\Html::a('删除',['permission-delete','name'=>$model->name],['class'=>'btn btn-danger btn-sm']):''?>
        </td>
    </tr>
    <?php endforeach;?>
</table>
