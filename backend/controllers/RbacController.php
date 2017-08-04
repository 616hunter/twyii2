<?php

namespace backend\controllers;

use backend\filters\RbacFilter;
use backend\models\PermissionForm;
use backend\models\RoleForm;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class RbacController extends \yii\web\Controller
{
    //添加权限
    public function actionAddPermission()
    {
        $model = new PermissionForm();
        $model->scenario = PermissionForm::SCENARIO_ADD;
        if (\yii::$app->request->isPost) {
            if ($model->load(\yii::$app->request->post()) && $model->validate()) {
                $authManager = \yii::$app->authManager;
                $permission = $authManager->createPermission($model->name);
                $permission->description = $model->description;
                $authManager->add($permission);
                \yii::$app->session->setFlash('success', '添加成功');
                return $this->redirect('permission-index');
            }
        }

        return $this->render('add-permission', ['model' => $model]);
    }

    public function actionPermissionEdit($name)
    {
        $authManager = \yii::$app->authManager;
        //找到对应的数据
        $permission = $authManager->getPermission($name);

        if ($permission == null) {
            throw new NotFoundHttpException('权限不存在');
        }
        $model = new PermissionForm();
        if (\yii::$app->request->isPost) {
            if ($model->load(\yii::$app->request->post()) && $model->validate()) {
                $authManager = \yii::$app->authManager;
                $permission->name = $model->name;
                $permission->description = $model->description;
                $permission = $authManager->update($name, $permission);
                \yii::$app->session->setFlash('success', '添加成功');
                return $this->redirect(['permission-index']);
            }
        } else {
            $model->name = $permission->name;
            $model->description = $permission->description;

        }
        return $this->render('add-permission', ['model' => $model]);

    }


    public function actionIndex()
    {
        return $this->render('index');
    }

    //获取所有权限
    public function actionPermissionIndex()
    {
        $authManager = \yii::$app->authManager;
        $model = $authManager->getPermissions();
        return $this->render('permission-index', ['models' => $model]);
    }

    //删除权限
    public function actionPermissionDelete($name)
    {
        $authManager = \yii::$app->authManager;
        $permission = $authManager->getPermission($name);
//        var_dump($permission);exit;
        $authManager->remove($permission);
        return $this->redirect(['permission-index']);
    }

    //添加角色
    public function actionRoleAdd()
    {
        $model = new RoleForm();
        $model->scenario = RoleForm::SCENARIO_ADD;
        $authManager = \yii::$app->authManager;
        //查询出所有的权限名
        $permissions = $authManager->getPermissions();
        //判断传值方式
        if (\yii::$app->request->isPost) {
            if ($model->load(\yii::$app->request->post()) && $model->validate()) {
                //实例化authManager
                $authManager = \yii::$app->authManager;
                //根据名字创建角色
                $role = $authManager->createRole($model->name);
                //得到描述内容
                $role->description = $model->description;
                //添加到角色表中
                $authManager->add($role);
                //给角色赋予权限
                if (is_array($model->permissions)) {
                    foreach ($model->permissions as $permissionName) {
                        $permission = $authManager->getPermission($permissionName);
                        if ($permission) $authManager->addChild($role, $permission);
                    }
                }
            }
            return $this->redirect(['role-index']);
        }
        return $this->render('role-add', ['model' => $model, 'permission' => $permissions]);
    }

    public function actionRoleIndex()
    {
        $authManager = \yii::$app->authManager;
        $models = $authManager->getRoles();
//        var_dump($models);exit;
        return $this->render('role-index', ['models' => $models]);
    }

    public function actionRoleDelete($name)
    {
        $authManager = \yii::$app->authManager;
        $model = $authManager->getRole($name);
        //var_dump($model);exit;
        $authManager->remove($model);
        return $this->redirect(['role-index']);
    }

    public function actionRoleEdit($name)
    {
        $model = new RoleForm();
        //根据角色得到权限
        $authManager = \yii::$app->authManager;
        $permissions = $authManager->getPermissionsByRole($name);
//        if($permissions==null){
//            throw new NotFoundHttpException(['该权限不存在'],404);
//        }
        //根据名字得到角色信息
        $role = $authManager->getRole($name);
        $model->name = $role->name;
        $model->description = $role->description;
        //得到所有权限
        $permissions = $authManager->getPermissionsByRole($name);
        $model->permissions = ArrayHelper::map($permissions, 'name', 'name');
        if (\yii::$app->request->isPost) {
            if ($model->load(\yii::$app->request->post()) && $model->validate()) {
                //取消关联
                $authManager->removeChildren($role);
                //赋值
                $role->name=$model->name;
                $role->description = $model->description;
               $authManager->update($name,$role);
                //给角色赋予权限
                if (is_array($model->permissions)) {
//                    var_dump($model->permissions);exit;
                    foreach ($model->permissions as $permissionName) {
                        $permission = $authManager->getPermission($permissionName);
                        if($permission) $authManager->addChild($role,$permission);
                    }
                    return $this->redirect(['permission-index']);
                }
            }
        }
        return $this->render('role-add', ['model' => $model]);
    }
    public function behaviors(){
        return [
            'rbac'=>[
                'class'=>RbacFilter::className(),
            ]
        ];
    }
}