<?php
namespace frontend\controllers;
use yii\web\Controller;

class CommonController extends Controller{
    public function actionHeader(){
        return $this->render('header');
    }
}