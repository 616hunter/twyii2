<table class="table table-condensed table-responsive table-responsive table-bordered table-striped">
    <tr class="text text-center text-info text-uppercase">
        <td>ID</td>
        <td>品牌名</td>
        <td>品牌简介</td>
        <td>品牌封面</td>
        <td>排序</td>
        <td>状态</td>
        <td>操作</td>
    </tr>
    <?php foreach($models as $model):?>
        <tr class="text text-center text-capitalize">
            <td><?=$model->id?></td>
            <td><?=$model->name?></td>
            <td><?=$model->intro?></td>
            <td><?=\yii\bootstrap\Html::img($model->logo,['height'=>50,'width'=>50])?></td>
            <td><?=$model->sort?></td>
            <td><?=\backend\models\Brand::statusOption($model->status)?></td>
            <td>
                <?=\yii::$app->user->can('brand/edit')?\yii\bootstrap\Html::a('修改',['brand/edit','id'=>$model->id],['class'=>'btn btn-info']):''?>
                <?=\yii::$app->user->can('brand/delete')?\yii\bootstrap\Html::a('删除',['brand/delete','id'=>$model->id],['class'=>'btn btn-danger']):''?>
            </td>
        </tr>
    <?php endforeach;?>
</table>
<?php echo \yii\widgets\LinkPager::widget(['pagination'=>$pager]);