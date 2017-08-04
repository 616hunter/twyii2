<?php

namespace backend\controllers;

use backend\filters\RbacFilter;
use backend\models\Menu;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class MenuController extends \yii\web\Controller
{
    public function actionIndex(){
        $models=Menu::find()->where('parent_id>=1')->All();
        return $this->render('index',['models'=>$models]);
    }
    public function actionAdd(){
        $model=new Menu();
        $authManager=\yii::$app->authManager;
        if(\yii::$app->request->isPost){
            if($model->load(\yii::$app->request->post())&&$model->validate()){
                $model->parent_id=$model->menu;
//                var_dump($model);exit;
                $model->url=$model->permissions;
                $model->save();
                return $this->redirect(['menu/index']);
            }
        }else{
            //得到所有的权限
            $menu=Menu::find()->All();
            $model->menu=$menu;
        }
        return $this->render('add',['model'=>$model]);
    }
    public function actionEdit($id){
        $model=Menu::findOne($id);
        if($model==null){
            throw new NotFoundHttpException('用户信息不存在');
        }
//        var_dump($model);exit;
        //思路对于数据库没有的要专门赋值才能选中
        if(\yii::$app->request->isPost){
            if($model->load(\yii::$app->request->post())&&$model->validate()){
                $model->url=$model->permissions;
                $model->parent_id=$model->menu;
                //不能移动到自己的分类下面
//                if()
//                if($model->parent_id==1&&$model->parent_id!=Menu::findOne($id)->parent_id){
//                    throw new NotFoundHttpException('');
//                }

                $model->save();
                return $this->redirect(['menu/index']);
            }
        }else{
            $model->menu=$model->parent_id;
            $model->permissions= $model->url;
        }
        return $this->render('add',['model'=>$model]);
    }
    //删除
    public function actionDelete($id){
        $model=Menu::findOne($id);
        //判断是否有下级菜单
        $count=Menu::findOne(['parent_id'=>$id]);
        //当选中的菜单有下级菜单时不能直接删除
        if($count){
            \yii::$app->session->setFlash('danger','该分类下有下级菜单不能删除');
        }else{
            $model->delete();
        }
        return $this->redirect(['menu/index']);
    }
    public function behaviors(){
        return [
            'rbac'=>[
                'class'=>RbacFilter::className(),
            ]
        ];
    }
}
