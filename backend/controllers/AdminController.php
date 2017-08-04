<?php

namespace backend\controllers;

use backend\filters\RbacFilter;
use backend\models\Admin;
use backend\models\AdminForm;
use backend\models\PasswordForm;
use yii\captcha\CaptchaAction;
use yii\helpers\ArrayHelper;

class AdminController extends \yii\web\Controller{

    //>>>>>>>>>>>>>>>>>>>>>>>>>>>添加功能>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
    public function actionAdd(){
        $model=new Admin();
        if($model->load(\yii::$app->request->post())&&$model->validate()){
           //将表单提交的明文密码加密
            $model->password_hash=\yii::$app->security->generatePasswordHash($model->password_hash);
            $model->save();
            $authManager=\yii::$app->authManager;
                if (is_array($model->roles)) {
                    foreach ($model->roles as $roleName) {
                        $role= $authManager->getRole($roleName);
                        if($role)$authManager->assign($role,$model->id);
                        \yii::$app->session->setFlash('success','用户信息添加成功');
                        return $this->redirect(['admin/index']);
                    }
        }
        }
        return $this->render('add',['model'=>$model]);
    }

    //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>首页显示>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
    public function actionIndex(){
        $model=Admin::find()->all();
        return $this->render('index',['models'=>$model]);
    }

    //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>修改功能>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
    public function actionDelete($id){
        $model=Admin::findOne(['id'=>$id]);
        $model->delete();
        \yii::$app->session->setFlash('danger','删除成功');
        return $this->redirect(['admin/index']);
    }
    //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>修改功能>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
    public function actionEdit($id){
        $model=Admin::findOne(['id'=>$id]);
        $authManager=\yii::$app->authManager;
        $roles=$authManager->getRolesByUser($id);
        $model->roles=ArrayHelper::map($roles,'name','name');
        if($model->load(\yii::$app->request->post())&& $model->validate()){
            if(!$model->password_hash==$model->password_hash){
                //将明文密码加密
                $model->password_hash=\yii::$app->security->generatePasswordHash($model->password_hash);
            }
            $model->save();
            $authManager->revokeAll($id);
            if (is_array($model->roles)) {
                foreach ($model->roles as $roleName) {
                    $role= $authManager->getRole($roleName);
                    if($role)$authManager->assign($role,$model->id);
                }

            }
            \yii::$app->session->setFlash('success','用户信息修改、保存成功');
            return $this->redirect(['admin/index']);
        }
        return $this->render('add',['model'=>$model]);
    }
    //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>登陆功能>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
    public function actionLogin()
    {
        $model = new AdminForm();
        if (\yii::$app->request->isPost) {
            $model->load(\yii::$app->request->post());
            if ($model->validate() && $model->login()) {
                $admin=new Admin();
                $admin=$admin->findOne(['username'=>$model->username]);
                //获取最后登陆时间和ip地址
                $admin->last_login_time=time();
                $admin->last_login_ip=\yii::$app->request->userIP;
                $admin->save();
                \yii::$app->session->setFlash('success','登录成功');
                return $this->redirect(['admin/index']);
            }
        }
        return $this->render('login', ['model' => $model]);
    }
    //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>修改密码>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
    public function actionPassword(){
        $model=PasswordForm::findOne(['id'=>\yii::$app->user->identity->id]);
        if(!\yii::$app->user->identity){
            \yii::$app->session->setFlash('danger','未登录，请先登录');
            return $this->redirect(['admin/login']);
        }else{
                if($model->load(\yii::$app->request->post())&&$model->validate()){
                    //先判断输入的旧密码是否和数据库密码一致
                    if(\yii::$app->security->validatePassword($model->oldPassword,\yii::$app->user->identity->password_hash)){
                        //判断两次输入的密码是否一致
                        if($model->newPassword!=$model->confirmPassword){
                            $model->addError('newPassword','两次密码输入不相同');

                        }else{
                            if($model->newPassword===$model->oldPassword){
                                $model->addError('confirmPassword','新密码和旧密码输入不能相同');
                            }
                            //将提交过来的新密码加密
                            $model->password_hash=\yii::$app->security->generatePasswordHash($model->newPassword);
                            //格式化更新时间
                            $model->update_at=time();
                            $model->save();
                            \yii::$app->session->setFlash('success','密码修改成功');
                            return $this->redirect(['admin/index']);
                        };
                    }else{
                    $model->addError('oldPassword','旧密码错误');
                }
                }
            return $this->render('password',['model'=>$model]);
        }
    }

    //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>注销>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
    public function actionLogout(){
        \yii::$app->user->logout();
        return $this->redirect(['admin/login']);
    }


}
