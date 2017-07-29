<?php

/* @var $this \yii\web\View */
/* @var $content string */

use backend\assets\AppAsset;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use common\widgets\Alert;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => '后台',
        'brandUrl' => '/goods/index',
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    $menuItems=[];
    $model=\backend\models\Menu::find()->where('parent_id=1')->all();
    //得到所有的数组
    foreach($model as $v){
        $items=[];
        //如果有下级分类就得到对应的地址
         $menus=\backend\models\Menu::find()->where('parent_id='.$v->id)->all();
         //存在就生成对应的数据
             foreach($menus as $menu){
                 //判断权限
                 if(\yii::$app->user->can($menu->url)){
                     //如果有子级就添加到$menuItems中
                     $items[]=['label'=>$menu->name,'url'=>'/'.$menu->url];
                 }
             }
         if(!empty($items)){
             $menuItems[]=['label'=>$v->name, 'items'=>$items];
         }
     }
    //是游客就显示登录，不是游客就显示内容
    if (Yii::$app->user->isGuest) {
        $menuItems[] = ['label' => '登录', 'url' => ['admin/login']];
    } else {
//        $menuItems[] = '<li>'
//            . Html::beginForm(['/admin/password'], 'get')
//            . Html::submitButton(
//                '修改密码 ',
//                ['class' => 'btn btn-link logout']
//            )
//            . Html::endForm().
//                '</li>'.
//                '<li>'
//            . Html::beginForm(['/admin/logout'], 'post')
//            . Html::submitButton(
//                '注销 (' . Yii::$app->user->identity->username. ')',
//                ['class' => 'btn btn-link logout']
//            )
//            . Html::endForm()
//            . '</li>';
        $menuItems[]=['label'=>'个人中心','items'=>[['label'=>'退出('.\yii::$app->user->identity->username.')','url'=>'/admin/logout'],
                                        ['label'=>'修改密码','url'=>'/admin/password']]
        ];
    }
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => $menuItems,
    ]);
    NavBar::end();
    ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy;后台管理 <?= date('Y') ?></p>

        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
