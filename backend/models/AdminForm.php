<?php
namespace backend\models;
use yii\base\Model;

class AdminForm extends Model{
    public $remember=false;
    public $username;
    public $password_hash;

    public function rules(){
        return [
                [['username','password_hash'],'required'],
                ['remember','safe']
               ];
    }

    public function attributeLabels(){
        return [
                'username'=>'用户名',
                'password_hash'=>'密码',
                'remember'=>'自动登录'
               ];
    }
    //登录模型
    public function login(){
        //先确认数据库有没有用户名，没有就直接返回用户名不存在，有就验证密码
        $admin=Admin::findOne(['username'=>$this->username]);
        //验证信息
        if($admin){
            if(\yii::$app->security->validatePassword($this->password_hash,$admin->password_hash)) {
                 \yii::$app->user->login($admin,$this->remember?24*3600:0);
                 return true;
            }else{
                 \yii::$app->session->setFlash('danger','密码错误');;
                }
        }else{
            $this->addError('password_hash','用户名不存在');
         }
         return false;
 }
}