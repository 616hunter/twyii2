<?php
namespace backend\models;
use yii\base\Model;

class RoleForm extends Model{
    const SCENARIO_ADD='add';
    public $name;
    public $description;
    public $permissions=[];
    public function rules(){
        return [
            [['name','description'],'required'],
            ['name','validateName','on'=>self::SCENARIO_ADD],
            ['permissions','safe']
        ];
    }
    public function attributeLabels(){
        return [
            'name'=>'名称',
            'description'=>'描述',
            'permissions'=>'权限'
        ];
    }
    public function validateName(){
        $authManager=\yii::$app->authManager;
        if($authManager->getRole($this->name)){
            $this->addError('name','该角色已经存在');
        };
    }
}