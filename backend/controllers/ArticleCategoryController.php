<?php

namespace backend\controllers;

use backend\filters\RbacFilter;
use backend\models\ArticleCategory;
use yii\web\Request;

class ArticleCategoryController extends \yii\web\Controller
{
    //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>首页>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
    public function actionIndex(){
        $models=ArticleCategory::find()->all();
        return $this->render('index',['models'=>$models]);
    }

    //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>逻辑删除>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
    public function actionDelete($id){
        $model=new ArticleCategory();
        $row=$model->findOne($id);
        $row->status=-1;
        $row->save();
        return $this->redirect(['article-category/index']);
    }

    //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>添加功能>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
    public function actionAdd(){
        $model=new ArticleCategory();
        $request=new Request();
        if($request->isPost){
            $model->load($request->post());
            if($model->validate()){
                $model->save();
                return $this->redirect(['article-category/index']);
            }
        }
        return $this->render('add',['model'=>$model]);
    }
    //>>>>>>>>>>>>>>>>>>>>>>>>>>>修改功能>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
    public function actionEdit($id){
        $model=ArticleCategory::findOne($id);
        $row=$model->findOne($id);
        $request=new Request();
        if($request->isPost){
            $model->load($request->post());
            if($model->validate()){
                $model->save();
                return $this->redirect(['article-category/index']);
            }
        }
        return $this->render('add',['model'=>$row]);
    }
    public function behaviors(){
        return [
            'rbac'=>[
                'class'=>RbacFilter::className(),
            ]
        ];
    }
}
