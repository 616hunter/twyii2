<?=\yii\bootstrap\Html::a('添加',['role-add'],['class'=>'btn btn-success'])?>
<table class="table table-hover table-condensed table-bordered table-responsive table-striped ">
    <tr>
        <td>角色名</td>
        <td>简介</td>
        <td>操作</td>
    </tr>
    <?php foreach($models as $model):?>
        <tr>
            <td><?=$model->name?></td>
            <td><?=$model->description?></td>
            <td>
                <?=\yii::$app->user->can('rbac/role-edit')?\yii\bootstrap\Html::a('修改',['role-edit','name'=>$model->name],['class'=>'btn btn-info']):''?>
                <?=\yii::$app->user->can('rbac/role-delete')?\yii\bootstrap\Html::a('删除',['role-delete','name'=>$model->name],['class'=>'btn btn-danger']):''?>
            </td>
        </tr>
    <?php endforeach;?>
</table>