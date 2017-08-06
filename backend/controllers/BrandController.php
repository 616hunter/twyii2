<?php

namespace backend\controllers;

use backend\filters\RbacFilter;
use backend\models\Brand;
use yii\data\Pagination;
use yii\web\Request;
use yii\web\UploadedFile;
use flyok666\uploadifive\UploadAction;
use flyok666\qiniu\Qiniu;


class BrandController extends \yii\web\Controller
{
    //>>>>>>>>>>>>>>>>>>>>>>>>>添加品牌>>>>>>>>>>>>>>>>>>>>>>>>>>
    public function actionAdd()
    {
        //实例化brand模型
        $model = new Brand();
        //判断传值方式
        if ($model->load(\yii::$app->request->post())&&$model->validate()) {
            $model->save();
                return $this->redirect(['brand/index']);
            }
        return $this->render('add', ['model' => $model]);
    }

    //>>>>>>>>>>>>>>>>>>>首页显示>>>>>>>>>>>>>>>>>>>>>
    public function actionIndex()
    {
        $query = Brand::find();
        $total = $query->count();
        $perPage=5;
            //分页工具类
        $pager=new Pagination([
           'totalCount'=>$total,
            'defaultPageSize'=>$perPage,
        ]);
        $models=$query->where('status>=0')->limit($pager->limit)->offset($pager->offset)->orderBy('sort')->all();
        return $this->render('index', ['models' => $models,'pager'=>$pager]);
    }

    //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>删除功能>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
    public function actionDelete($id)
    {
        $model = new Brand();
        $row = $model->findOne($id);
        $row->status=-1;
        $row->save();
        return $this->redirect(['brand/index']);
    }

    //>>>>>>>>>>>>>>>>>>>>修改功能>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
    public function actionEdit($id){
        $model = Brand::findOne($id);
        if($model->load(\yii::$app->request->post())&&$model->validate()){
            $model->save();
                return $this->redirect(['brand/index']);
            }
        return $this->render('add', ['model' => $model]);
    }

//uploadifive 组件
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
                   // $action->output['fileUrl'] = $action->getWebUrl();//输出文件的相对路径
//                    $action->getFilename(); // "image/yyyymmddtimerand.jpg"
//                    $action->getWebUrl(); //  "baseUrl + filename, /upload/image/yyyymmddtimerand.jpg"
//                    $action->getSavePath(); // "/var/www/htdocs/upload/image/yyyymmddtimerand.jpg"
                //将图片上传到七牛云
                    $qiniu = new Qiniu(\Yii::$app->params['qiniu']);
                    $qiniu->uploadFile(
                        $action->getSavePath(),
                        $action->getWebUrl()
                    );
                    $url = $qiniu->getLink($action->getWebUrl());
                    $action->output['fileUrl'] =$url;
                },
            ],
        ];
    }
    //测试七牛云
    public function actionQiniu(){
        $config = [
            'adminEmail' => 'admin@example.com',
            'qiniu'=>[
                'accessKey'=>'a82U-_KGj7QfycEkwR6O6n4Nbi6wqsyUAYAJdvBq',
                'secretKey'=>'8o4TNpRNbp-8CCY7Mu0VsB6VEKqeUbSAwMZNfzru',
                'domain'=>'http://otbu037ng.bkt.clouddn.com/',
                'bucket'=>'twyiishop',
                'area'=>Qiniu::AREA_HUADONG,
        ],];
        $qiniu = new Qiniu($config);
        $key = time();
        $qiniu->uploadFile($_FILES['tmp_name'],$key);
        $url = $qiniu->getLink($key);
    }
//    public function behaviors(){
//        return [
//            'rbac'=>[
//                'class'=>RbacFilter::className(),
//            ]
//        ];
//    }
}
