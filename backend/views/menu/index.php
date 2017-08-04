<table class="table table-hover table-bordered table-condensed table-striped table-responsive">
    <tr class="text text-center text-danger success">
        <th class="text-center">菜单名称</th>
        <th class="text-center">网址(路由)</th>
        <th class="text-center">所属菜单</th>
        <th class="text-center">排序</th>
        <th class="text-center">操作</th>
    </tr>
    <?php foreach($models as $model):?>
        <tr class="text-center">
            <td><?=$model->name?></td>
            <td><?=$model->url?></td>
            <td><?=\backend\models\Menu::findOne(['id'=>$model->parent_id])? \backend\models\Menu::findOne(['id'=>$model->parent_id])->name:'最上级'?></td>
            <td><?=$model->sort?></td>
            <td>
                <?=\yii::$app->user->can('menu/edit')?\yii\bootstrap\Html::a('修改',['menu/edit','id'=>$model->id],['class'=>'btn btn-info btn-xs']):''?>
                <?=\yii::$app->user->can('menu/delete')?\yii\bootstrap\Html::a('删除',['menu/delete','id'=>$model->id],['class'=>'btn btn-danger btn-xs']):0?>
            </td>
        </tr>
    <?php endforeach;?>
</table>