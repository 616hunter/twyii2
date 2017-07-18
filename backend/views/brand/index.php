<table class="table table-condensed table-responsive table-responsive table-bordered table-striped">
    <tr>
        <td>ID</td>
        <td>品牌名</td>
        <td>品牌简介</td>
        <td>品牌封面</td>
        <td>排序</td>
        <td>状态</td>
        <td>操作</td>
    </tr>
    <?php foreach($models as $model):?>
        <tr>
            <td><?=$model->id?></td>
            <td><?=$model->name?></td>
            <td><?=$model->intro?></td>
            <td><?=\yii\bootstrap\Html::img($model->logo,['height'=>80])?></td>
            <td><?=$model->sort?></td>
            <td><?=\backend\models\Brand::statusOption($model->status)?></td>
            <td>
                <?=\yii\bootstrap\Html::a('修改',['brand/edit','id'=>$model->id],['class'=>'btn btn-info'])?>
                <?=\yii\bootstrap\Html::a('删除',['brand/delete','id'=>$model->id],['class'=>'btn btn-danger'])?>
            </td>
        </tr>
    <?php endforeach;?>
</table>