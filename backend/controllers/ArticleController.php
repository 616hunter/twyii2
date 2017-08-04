<?php

namespace backend\controllers;

use backend\filters\RbacFilter;
use backend\models\Article;
use backend\models\ArticleCategory;
use backend\models\ArticleDetail;
use yii\data\Pagination;
use yii\web\Request;

class ArticleController extends \yii\web\Controller{
    public function actionIndex(){
//        $category=ArticleCategory::find()->all();
        $query=Article::find();
        $total=$query->count();
        $perPage=5;
        $pager=new Pagination([
            'totalCount'=>$total,
            'defaultPageSize'=>$perPage,
        ]);
        $articles=$query->where('status>=0')->limit($pager->limit)->offset($pager->offset)->orderBy('sort')->all();
//        $model2=new ArticleDetail();
////        $details=$model2->find()->all();
//        $model3=new ArticleCategory();
//        $rows=$model3->find()->all();
        return $this->render('index',['articles'=>$articles,'pager'=>$pager]);
    }
    public function actionAdd(){
        //实例化文章模型
        $model=new Article();
        //实例化文章内容模型
        $model2=new ArticleDetail();
        //文章分类模型
        $model3=ArticleCategory::find()->all();
//        $row=$model3->find()->all();
        $request=new Request();
        if($request->isPost){
            $model2->load($request->post());
            $model->load($request->post());
            if($model->validate()&&$model2->validate()){
                $model->create_time=time();
//                var_dump($model->create_time);exit;
                $model->save();
                $model->article_category_id=$model->id;
//                var_dump($model->id);exit;
                $model2->article_id=$model->id;
                $model2->save();
                return $this->redirect(['article/index']);
            }
        }
        return $this->render('add',['model'=>$model,'model2'=>$model2,'row'=>$model3]);
    }

//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>删除功能>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
    public function actionDelete($id){
        $query=new Article();
        $model=$query->findOne($id);
        $model->status=-1;
        $model->save();
        return $this->redirect(['article/index']);
    }
    //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>修改功能>>>>>>>>>>>>>>>>
    public function actionEdit($id){
        $model=Article::findOne($id);
        $model2=ArticleDetail::findOne($id);
//                var_dump($model2);exit;
//        $rows=$model2->find()->all();
        $model3=ArticleCategory::find()->all();
        $request=new Request();
        if($request->isPost){
          $model->load($request->post());
            $model2->load($request->post());
            if($model->validate() && $model2->validate()){
                $model->save();
                $model2->save();
                return $this->redirect(['article/index']);
            }
        }
        return $this->render('add',['model'=>$model,'model2'=>$model2,'row'=>$model3]);
    }
    //>>>>>>>>>>>>>>>>>>>>>>>>>文章回收站>>>>>>>>>>>>>>>>>>>>>>>>>>>>>.....
    public function actionRecycle(){
        $query=Article::find();
        $model=$query->select('*')->where('status=-1')->all();
        return $this->render('recycle',['articles'=>$model]);
    }
    public function actionRecovery($id){
        $model=Article::findOne($id);
        $model2=ArticleCategory::findOne($id);
//        var_dump($model);exit;
        $model->status='0';
        $model->save();
        return $this->redirect(['article/index']);
}

    public function behaviors(){
        return [
            'rbac'=>[
                'class'=>RbacFilter::className(),
            ]
        ];
    }
}
