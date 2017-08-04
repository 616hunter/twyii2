
<table class="table table-hover table-striped table-bordered table-condensed">
    <tr class="text text-center danger">
        <td>分类名称</td>
        <td>父分类</td>
        <td>简介</td>
        <td>操作</td>
    </tr>
    <?php foreach($rows as $row):?>
        <tr class="text text-center ">
            <td><?php echo $row->name;?></td>
            <td><?php
                if($row->parent_id>0){
                    $model=\backend\models\GoodsCategory::findOne(['id'=>$row->parent_id]);
                    echo $model->name;
                }
              else{
                  echo '最顶级分类';
              }
                ?></td>
            <td><?=$row->intro?></td>
            <td class="text-left">
                <?=\yii::$app->user->can('goods-category/edit')?\yii\bootstrap\Html::a('修改',['goods-category/edit','id'=>$row->id],['class'=>'btn btn-info btn-sm']):''?>
                <?php if($row->depth==0){
                    echo '';
                }else{
                    echo \yii::$app->user->can('goods-category/delete')?\yii\bootstrap\Html::a('删除',['goods-category/delete','id'=>$row->id],['class'=>'btn btn-danger btn-sm']):'';
                };
                ?>
            </td>
        </tr>
    <?php endforeach;?>
</table>