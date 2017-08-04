<table class="table table-condensed table-hover table-striped table-responsive">
    <tr>
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
        <tr>
            <td><?=$article->id?></td>
            <td><?=$article->name?></td>
            <td><?=$article->articleCategory->name?></td>
            <td><?=$article->intro?></td>
            <td><?=$article->status?></td>
            <td><?=\backend\models\Article::statusOption($article->status)?></td>
            <td><?=date('Y-m-d H:i:s',$article->create_time)?></td>
            <td>
                <?=\yii\bootstrap\Html::a('恢复',['article/recovery','id'=>$article->id],['class'=>'btn btn-info btn-sm'])?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>