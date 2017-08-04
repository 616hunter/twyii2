<?php $form = \yii\bootstrap\ActiveForm::begin(
[
    'action' => ['index'],
     'method' => 'get',
      'id' => '',
      'options' => ['class' => 'form-inline'],
]);
 echo  $form->field($search, 'name',['options'=>['class'=>'form-group'],
                'inputOptions' => ['placeholder' => '商品名称','class' => ' form-control'],
])->label(false);echo  '---';
echo  $form->field($search, 'sn',['options'=>['class'=>'form-group'],
                'inputOptions' => ['placeholder' => '货号','class' => ' form-control'],
])->label(false) ;echo  '---';
echo  $form->field($search, 'shop_price',['options'=>['class'=>'form-group',],
                'inputOptions' => ['placeholder' => '最低价格','class' => ' form-control'],
])->label(false) ;echo  '---';
echo  $form->field($search, 'market_price',['options'=>['class'=>'form-group'],
                 'inputOptions' => ['placeholder' => '最高价格','class' => ' form-control'],
])->label(false) ;
?>
<span class="form-group-btn">
<?php echo \yii\bootstrap\Html::submitButton('', ['class' => 'btn  btn-primary glyphicon glyphicon-search']) ?>
</span>
<?php \yii\bootstrap\ActiveForm::end(); ?>

<table class="table table-hover table-condensed table-striped table-bordered table-responsive">
    <tr class="text-center text-danger">
        <th class="text-center">商品名称</th>
        <th class="text-center">货号</th>
        <th class="text-center">商品logo</th>
        <th class="text-center">商品所属分类</th>
        <th class="text-center">所属品牌</th>
        <th class="text-center">超市价格</th>
        <th class="text-center">本店价格</th>
        <th class="text-center">库存</th>
        <th class="text-center">是否在售</th>
        <th class="text-center">状态</th>
        <th class="text-center">排序</th>
        <th class="text-center">录入时间</th>
        <th class="text-center">浏览次数</th>
        <th class="text-center">操作</th>
        </tr>
    <?php foreach($models as $model):?>
        <tr class="text-center">
            <td><?=mb_substr($model->name,0,5)?></td>
            <td><?=$model->sn?></td>
            <td><?=\yii\bootstrap\Html::img($model->logo?$model->logo:'暂无图片',['height'=>50,'width'=>50,'cellspacing'=>0])?></td>
            <td><?=\backend\models\GoodsCategory::findOne(['id'=>$model->goods_category_id])->name?></td>
            <td><?=\backend\models\Brand::findOne(['id'=>$model->brand_id])->name?></td>
            <td><?=$model->market_price?></td>
            <td><?=$model->shop_price?></td>
            <td><?=$model->stock?></td>
            <td><?=$model->is_on_sale=1?'在售':'下架'?></td>
            <td><?=\backend\models\Goods::statusOption($model->status)?></td>
            <td><?=$model->sort?></td>
            <td><?=date('Y-m-d H:i:s',$model->create_time)?></td>
            <td><?=$model->view_times.'次'?></td>
            <td>
                <?=\yii::$app->user->can('goods/edit')?\yii\bootstrap\Html::a('',['goods/edit','id'=>$model->id],['class'=>'glyphicon glyphicon-pencil btn btn-info btn-sm']):''?>
                <?=\yii::$app->user->can('goods/delete')?\yii\bootstrap\Html::a('',['goods/delete','id'=>$model->id],['class'=>'glyphicon glyphicon-trash btn btn-danger btn-sm']):''?>
            </td>
        </tr>
    <?php endforeach;?>
</table>
<?php echo \yii\widgets\LinkPager::widget(['pagination'=>$pager]);

