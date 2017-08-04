<?php

namespace frontend\controllers;

use app\models\ShopAddress;
use backend\models\GoodsCategory;
use frontend\models\Goods;

class GoodsController extends \yii\web\Controller
{
    public $layout=false;
    public $enableCsrfValidation=false;
    public function actionIndex(){
        $GoodsCategory=GoodsCategory::find()->all();

        return $this->render('index',['goods_categories'=>$GoodsCategory]);
    }
    //商品列表页面
    public function actionList($id){
        $level=GoodsCategory::findOne(['id'=>$id]);
//        var_dump($level);exit;
        if($level->depth==2){
            $goods=\backend\models\Goods::find()->where('goods_category_id='.$id)->asArray()->all();

        }else{
            $ids=$level->leaves()->asArray()->column();
            $goods=\backend\models\Goods::find()->where(['in','goods_category_id',$ids])->asArray()->all();
        }
        return $this->render('list',['goods'=>$goods]);
    }
    //商品详情页面
    public function actionGoods($id){
        $goods=\backend\models\Goods::findOne(['id'=>$id]);
        $goodsIntro=\backend\models\GoodsIntro::findOne(['goods_id'=>$id]);
        return $this->render('goods',['goods'=>$goods,'intro'=>$goodsIntro]);
    }

}
