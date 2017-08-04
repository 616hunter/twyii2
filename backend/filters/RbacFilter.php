<?php
namespace backend\filters;
use yii\base\ActionFilter;
use yii\web\ForbiddenHttpException;

class RbacFilter extends ActionFilter{
    public function beforeAction($action){
        if(!\yii::$app->user->can($action->uniqueId)){
            throw new ForbiddenHttpException('对不起，你暂时没有权限访问该文件，如有疑问，请联系管理员');
//            return false;
        }
        return parent::beforeAction($action);
    }

}