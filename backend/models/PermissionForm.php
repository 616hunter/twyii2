<?php
namespace backend\models;

use yii\base\Model;

class PermissionForm extends  Model{
    const SCENARIO_ADD='add';
    public $name;
    public $description;
    public function rules(){
        return [
          [['name','description'],'required'],
            ['name','validateName','on'=>self::SCENARIO_ADD]
        ];
    }
    public function attributeLabels(){
        return [
          'name'=>'名称',
            'description'=>'介绍'
        ];
    }

    public function validateName(){
        $authManager=\yii::$app->authManager;
        if($authManager->getPermission($this->name)){
            $this->addError('name','该权限已经存在');
        };
    }
}
