<?=\yii\bootstrap\Html::a('查看回收站',['article/recycle'],['class'=>'btn btn-success'])?>

<table class="table table-condensed table-hover table-bordered table-striped">
    <tr class="text-center">
        <td>Id</td>
        <td>文章名</td>
        <td>所属分类</td>
        <td>简介</td>
        <td>排序</td>
        <td>状态</td>
        <td>创建时间</td>
        <td>操作</td>
    </tr>
    <?php foreach($articles as $article):?>
        <tr class="text-center">
            <td><?=$article->id?></td>
            <td><?=$article->name?></td>
            <td><?=$article->articleCategory->name?></td>
            <td><?=$article->intro?></td>
            <td><?=$article->status?></td>
            <td><?=\backend\models\Article::statusOption($article->status)?></td>
            <td><?=date('Y-m-d H:i:s',$article->create_time)?></td>
            <td>
                <?=\yii::$app->user->can('article/edit')?\yii\bootstrap\Html::a('修改',['article/edit','id'=>$article->id],['class'=>'btn btn-info btn-sm']):''?>
                <?=\yii::$app->user->can('article/delete')?\yii\bootstrap\Html::a('删除',['article/delete','id'=>$article->id],['class'=>'btn btn-danger btn-sm']):''?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
<?php echo \yii\widgets\LinkPager::widget(['pagination'=>$pager])?>