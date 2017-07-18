<table class="table table-condensed table-striped table-bordered table-responsive table-hover">
    <tr>
        <td>ID</td>
        <td>文章名</td>
        <td>文章介绍</td>
        <td>排序</td>
        <td>文章状态</td>
        <td>操作</td>
    </tr>
    <?php foreach($models as $model):?>
        <tr>
            <td><?=$model->id?></td>
            <td><?=$model->name?></td>
            <td><?=$model->intro?></td>
            <td><?=$model->sort?></td>
            <td><?=\backend\models\Brand::statusOption($model->status)?></td>
            <td>
                <?=\yii\bootstrap\Html::a('修改',['article-category/edit','id'=>$model->id],['class'=>'btn btn-info'])?>
                <?=\yii\bootstrap\Html::a('删除',['article-category/delete','id'=>$model->id],['class'=>'btn btn-danger'])?>
            </td>

        </tr>
    <?php endforeach;?>
</table>