<?php

namespace backend\controllers;

use backend\models\Brand;
use yii\web\Request;
use yii\web\UploadedFile;

class BrandController extends \yii\web\Controller
{
    //>>>>>>>>>>>>>>>>>>>>>>>>>添加品牌>>>>>>>>>>>>>>>>>>>>>>>>>>
    public function actionAdd(){
        //实例化brand模型
        $model=new Brand();
        //实例化request模型
        $request=new Request();
        //判断传值方式
        if($request->isPost){
            $model->load($request->post());
            $model->logoFile=UploadedFile::getInstance($model,'logoFile');
//            echo 111;exit;
            if($model->validate()){
//                echo 111;exit;
                if($model->logoFile){
                    $d=\yii::getAlias('@webroot').'/upload/'.date('Ymd');
                    if(!is_dir($d)){
                        mkdir($d);
                    }
                    $fileName='/upload/'.date('Ymd').'/'.uniqid().'.'.$model->logoFile->extension;
                    $model->logoFile->saveAs(\yii::getAlias('@webroot').$fileName,false);
                    $model->logo=$fileName;
                }
                $model->save(false);
                echo 1111;

            } else{
                //验证失败 打印错误信息
                var_dump($model->getErrors());exit;
            }
        }
        return $this->render('add',['model'=>$model]);
    }


    public function actionIndex(){
        $model=new Brand();
        $models=$model->find()->all();
//        var_dump($models);exit;
        return $this->render('index',['models'=>$models]);
    }

}
