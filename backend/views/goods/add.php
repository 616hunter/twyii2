
<?php
$form=\yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'name');
//ueditor部分
echo $form->field($goods_intro,'content')->widget(kucha\ueditor\UEditor::className(),
    [
    'clientOptions'=>[
        'initialFrameHeight'=>'200',
        'lang'=>'zh-cn'
    ]
]
);
//图片上传部分
echo $form->field($model,'logo')->hiddenInput();
//外部TAG
echo \yii\bootstrap\Html::fileInput('test', NULL, ['id' => 'test']);
echo \flyok666\uploadifive\Uploadifive::widget([
    'url' => yii\helpers\Url::to(['goods/s-upload']),
    'id' => 'test',
    'csrf' => true,
    'renderTag' => false,
    'jsOptions' => [
        'formData'=>['someKey'    => 'someValue'],
        'width' => 120,
        'height' => 40,
        'onError' => new \yii\web\JsExpression(<<<EOF
function(file, errorCode, errorMsg, errorString) {
    console.log('The file ' + file.name + ' could not be uploaded: ' + errorString + errorCode + errorMsg);
}
EOF
        ),
        'onUploadComplete' => new \yii\web\JsExpression(<<<EOF
function(file, data, response) {
    data = JSON.parse(data);
//    console.log(data);
    if (data.error) {
        console.log(data.msg);
    } else {
        console.log(data.fileUrl);
        //将图片地址赋值给logo字段
        $("#goods-logo").val(data.fileUrl);
        //将上传成功的图片回显
        $("#img").attr('src',data.fileUrl,'height=80');
    }
}
EOF
        ),
    ]
]);
echo \yii\bootstrap\Html::img($model->logo?$model->logo:false,['id'=>'img','height'=>80]);
//分类树部分
echo $form->field($model,'goods_category_id')->hiddenInput();
echo '<div><ul id="treeDemo" class="ztree"></ul></div>';
//商品分类部分
echo $form->field($model,'brand_id')->dropDownList(\yii\helpers\ArrayHelper::map($brand,'id','name'));

echo $form->field($model,'market_price');
echo $form->field($model,'shop_price');
echo $form->field($model,'stock');
echo $form->field($model,'is_on_sale',['inline'=>true])->radioList([0=>'不上线',1=>'上线']);
echo $form->field($model,'status',['inline'=>true])->radioList(\backend\models\Goods::statusOptions());
echo $form->field($model,'sort')->textInput(['type'=>'number']);
echo\yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-success']);
\yii\bootstrap\ActiveForm::end();


//调用视图的方法加载静态资源
//加载css文件
$this->registerCssFile('@web/zTree/css/zTreeStyle/zTreeStyle.css');
//加载js文件
$this->registerJsFile('@web/zTree/js/jquery.ztree.core.js',['depends'=>\yii\web\JqueryAsset::className()]);
//加载js代码
$categories[] = ['id'=>0,'parent_id'=>0,'name'=>'顶级分类','open'=>1];
$nodes = \yii\helpers\Json::encode($categories);
$nodeId = $model1->parent_id;
$this->registerJs(new \yii\web\JsExpression(
    <<<JS
    var zTreeObj;
        // zTree 的参数配置，深入使用请参考 API 文档（setting 配置详解）
        var setting = {
            data: {
                simpleData: {
                    enable: true,
                    idKey: "id",
                    pIdKey: "parent_id",
                    rootPId: 0
                }
            },
            callback: {
		        onClick: function(event, treeId, treeNode){
		            //console.log(treeNode.id);没问题
		            //将当期选中的分类的id，赋值给parent_id隐藏域
		            $("#goods-goods_category_id").val(treeNode.id);
		        }
	        }
        };
        // zTree 的数据属性，深入使用请参考 API 文档（zTreeNode 节点数据详解）
        var zNodes = {$nodes};

        zTreeObj = $.fn.zTree.init($("#treeDemo"), setting, zNodes);
        zTreeObj.expandAll(true);//展开全部节点

        //获取节点
        var node = zTreeObj.getNodeByParam("id", "{$nodeId}", null);
        //选中节点
        zTreeObj.selectNode(node);
        //触发选中事件
JS
));