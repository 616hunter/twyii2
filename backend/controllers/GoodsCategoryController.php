<?php

namespace backend\controllers;
use backend\filters\RbacFilter;
use backend\models\GoodsCategory;
use yii\web\HttpException;
use yii\web\Request;

class GoodsCategoryController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $model=new GoodsCategory();
        $rows=$model->find()->all();
//        var_dump(count($rows));exit;
        $count=count($rows);
        return $this->render('index',['rows'=>$rows,'count'=>$count]);
    }

    public function actionAdd(){
        $model=new GoodsCategory(['parent_id'=>0]);
        $request=new Request();
        if($model->load($request->post())){
            if($model->validate()){
//                $model->save();
            if($model->parent_id){
                $category=GoodsCategory::findOne(['id'=>$model->parent_id]);
                if($category){
                    $model->prependTo($category);
                }else{
                    throw new HttpException(404,'上级分类不存在') ;
                }
            }else{
                $model->makeRoot();
            }
                \yii::$app->session->setFlash('success','添加分类成功');
            }

        }
        //得到所有分类的数据
        $categories = GoodsCategory::find()->select(['id','parent_id','name'])->asArray()->all();
        return $this->render('add',['model'=>$model,'categories'=>$categories]);
    }
    //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>删除功能>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
    public function actionDelete($id){
        $query=GoodsCategory::findOne(['id'=>$id]);
        $count=GoodsCategory::findOne(['parent_id'=>$query->id]);
        if($count){
            \yii::$app->session->setFlash('danger','该商品有下级分配，不能直接删除');
            return $this->redirect(['goods-category/index']);
        }else{
            $query->delete();
            return $this->redirect(['goods-category/index']);
        }
    }
    //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>修改功能>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
    public function actionEdit($id){
        $model=GoodsCategory::findOne($id);
        if($model->load(\yii::$app->request->post())&&$model->validate()){
            $model->save();
            return $this->redirect('index');
        }
        $categories = GoodsCategory::find()->select(['id','parent_id','name'])->asArray()->all();
        return $this->render('add',['model'=>$model,'categories'=>$categories]);
    }


    public function behaviors(){
        return [
            'rbac'=>[
                'class'=>RbacFilter::className(),
            ]
        ];
    }
}
