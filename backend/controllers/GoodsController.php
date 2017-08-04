<?php

namespace backend\controllers;

use backend\filters\RbacFilter;
use backend\models\Brand;
use backend\models\Goods;
use backend\models\GoodsCategory;
use backend\models\GoodsDayCount;
use backend\models\GoodsGallery;
use backend\models\GoodsIntro;
use flyok666\uploadifive\UploadAction;
use yii\data\Pagination;
use yii\web\Request;

class GoodsController extends \yii\web\Controller
{
    //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>商品表首页>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
    public function actionIndex(){
        $where=\yii::$app->request->get('Goods');
        $query=Goods::find()->where('status>=0')->orderBy('sort ASC');
        if(!empty($where['name']||!empty($where['sn']))||!empty($where['shop_price'])||!empty($where['market_price'])){
//            var_dump($where['name']);exit;
                    if(empty($where['shop_price'])){
            $where['shop_price']=1;
        }
        if(empty($where['market_price'])){
            $where['market_price']=10000000000000;
        }
            $query->andFilterWhere(['like','name',$where['name']])
                    ->andFilterWhere(['like','sn',$where['sn']])
                    ->andFilterWhere(['between','shop_price',$where['shop_price'],$where['market_price']]);
        }
        //查询出总条数
        $total=$query->count();
        //每页显示的条数
        $perPage=10;
        //实例化分页工具类，并传入总条数和每页显示的条数
        $pager=new Pagination([
            'totalCount'=>$total,
            'defaultPageSize'=>$perPage
        ]);
        $search=new Goods();
        //执行查询的语句
        $model=$query->limit($pager->limit)->offset($pager->offset)->all();
        return $this->render('index',['models'=>$model,'pager'=>$pager,'search'=>$search,'where'=>$where]);
    }

    //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>商品的添加功能>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
    public function actionAdd(){
        //实例化商品模型
        $model=new Goods();
        //实例化商品简介的模型
        $goods_intro=new GoodsIntro();
        //实例化商品分类模型
        $model1=new GoodsCategory(['parent_id'=>0]);
        //查询商品分类的id,name,parent_id;
        $categories = GoodsCategory::find()->select(['id','parent_id','name'])->asArray()->all();
        //查出品牌的所有数据
        $brand=Brand::find()->all();
        $request=new Request();
        if($model->load($request->post())&&$model->validate()&&$goods_intro->load($request->post())&&$goods_intro->validate()){
            $count=GoodsDayCount::findOne(['day'=>date('Y-m-d')]);
//            var_dump($count);exit;
            //判断是否是今天添加的商品
            if(empty($count)){
                //不是今天添加的，就初始化goods_day_count
                $day_count = new GoodsDayCount();
                $day_count ->day = date('Y-m-d',time());
                $day_count -> count = 0001;
                $day_count->save();
            }else{
                //是今天添加的，count加1
                $count -> count ++;
                $count ->save();
            }
        //sn商品货号
            if(empty($count)){
                //商品数量表里没数据，则新增为1
                $model->sn = date('Ymd',time()).str_pad('1',4,"0",STR_PAD_LEFT);
            }else{
                //商品数量表里有数据，则使用表里面的数据
                $model->sn = date('Ymd',time()).str_pad($count->count,4,"0",STR_PAD_LEFT);
            }
            //得到创建事件
            $model->create_time=time();
            //初始化浏览次数
            $model->view_times=0;
            $model->save();
            //得到商品简介表中的goods_id，并保存
            $goods_intro->goods_id=$model->id;
            $goods_intro->save();

            return $this->redirect(['goods-gallery/gallery','id'=>$model->id]);
        }
        return $this->render('add',['model'=>$model,'model1'=>$model1,'brand'=>$brand,'categories'=>$categories,'goods_intro'=>$goods_intro]);
    }

    //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>删除部分>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
    public function actionDelete($id){
        $model=Goods::findOne(['id'=>$id]);
        $goods_intro=GoodsIntro::findOne(['goods_id'=>$id]);
        $model->delete();
        $goods_intro->delete();
        return $this->redirect(['goods/index']);
    }

//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>修改部分>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
    public function actionEdit($id){
        //查询出对应的商品表信息
        $model=Goods::findOne(['id'=>$id]);
        $model1=new GoodsCategory(['parent_id'=>0]);
        //查处商品分类的id,name,parent_id;
        $categories = GoodsCategory::find()->select(['id','parent_id','name'])->asArray()->all();
        //查出品牌的所有数据
        $brand=Brand::find()->all();
        //查询出商品详情中的内容
        $goods_intro=GoodsIntro::findOne(['goods_id'=>$id]);
        $request=new Request();
        if($model->load($request->post())&& $model->validate()&&$goods_intro->load($request->post())&&$goods_intro->validate()){
            $model->save();
            $goods_intro->save();
            return $this->redirect(['goods/index']);
        }
        return $this->render('add',['model'=>$model,'model1'=>$model1,'categories'=>$categories,'brand'=>$brand,'goods_intro'=>$goods_intro]);

    }

//    public function actionUe(){
//        return [
//            'upload' => [
//                'class' => 'kucha\ueditor\UEditorAction',
//            ]
//        ];
//    }



    public function actions() {
        return [
            's-upload' => [
                'class' => UploadAction::className(),
                'basePath' => '@webroot/upload',
                'baseUrl' => '@web/upload',
                'enableCsrf' => true, // default
                'postFieldName' => 'Filedata', // default
                //BEGIN METHOD
                'format' => [$this, 'methodName'],
                //END METHOD
                //BEGIN CLOSURE BY-HASH
                'overwriteIfExist' => true,
//                'format' => function (UploadAction $action) {
//                    $fileext = $action->uploadfile->getExtension();
//                    $filename = sha1_file($action->uploadfile->tempName);
//                    return "{$filename}.{$fileext}";
//                },
                //END CLOSURE BY-HASH
                //BEGIN CLOSURE BY TIME
                'format' => function (UploadAction $action) {
                    $fileext = $action->uploadfile->getExtension();
                    $filehash = sha1(uniqid() . time());
                    $p1 = substr($filehash, 0, 2);
                    $p2 = substr($filehash, 2, 2);
                    return "{$p1}/{$p2}/{$filehash}.{$fileext}";
                },
                //END CLOSURE BY TIME
                'validateOptions' => [
                    'extensions' => ['jpg', 'png'],
                    'maxSize' => 1 * 1024 * 1024, //file size
                ],
                'beforeValidate' => function (UploadAction $action) {
                    //throw new Exception('test error');
                },
                'afterValidate' => function (UploadAction $action) {},
                'beforeSave' => function (UploadAction $action) {},
                'afterSave' => function (UploadAction $action) {
                     $action->output['fileUrl'] = $action->getWebUrl();//输出文件的相对路径
//                    $action->getFilename(); // "image/yyyymmddtimerand.jpg"
//                    $action->getWebUrl(); //  "baseUrl + filename, /upload/image/yyyymmddtimerand.jpg"
//                    $action->getSavePath(); // "/var/www/htdocs/upload/image/yyyymmddtimerand.jpg"
                    //将图片上传到七牛云
//                    $qiniu = new Qiniu(\Yii::$app->params['qiniu']);
//                    $qiniu->uploadFile(
//                        $action->getSavePath(),
//                        $action->getWebUrl()
//                    );
//                    $url = $qiniu->getLink($action->getWebUrl());
//                    $action->output['fileUrl'] =$url;
                },
            ],
        ];
    }
//    public function behaviors(){
//        return [
//            'rbac'=>[
//                'class'=>RbacFilter::className(),
//            ]
//        ];
//    }

}
