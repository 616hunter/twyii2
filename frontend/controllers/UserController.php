<?php

namespace frontend\controllers;

use Aliyun\Api\Sms\Request\V20170525\SendSmsRequest;
use Aliyun\Core\DefaultAcsClient;
use Aliyun\Core\Profile\DefaultProfile;
use app\models\Cart;
use app\models\Locations;
use app\models\ShopAddress;
use frontend\models\member;
use frontend\models\User;
use yii\captcha\CaptchaAction;
use Aliyun\Core\Config;

class UserController extends \yii\web\Controller{
    public $layout=false;
    public $enableCsrfValidation=false;
    public function actionIndex()
    {
        $model=new User();
        return $this->render('index');
    }
    //.......................................注册功能
    public function actionRegister(){
        $model=new User();
        $model->scenario = User::SCENARIO_REGISTER;
        $session=\yii::$app->session;
            if($model->load(\yii::$app->request->post())&&$model->validate()){
                    $model->created_at=time();
                    $model->password_hash=\yii::$app->security->generatePasswordHash($model->password);
                    $model->auth_key=\yii::$app->security->generateRandomString();
                    $model->status=1;
//                    var_dump($model->captcha);exit;
                if($model->password==$model->rePassword){
                    $model->save(false);
//                    var_dump($model->getErrors());exit;
//                    var_dump($session);
                    //验证手机号码
//                    var_dump($model->captcha,$session);exit;
                    if(!$session[$model->tel]==$model->captcha){
                        //验证不成功
                        return $this->redirect('register');
                    }
                    return $this->redirect(['user/login']);
                }else{
                    $model->addError('rePassword','两次密码不相同');
                }
            }
        return $this->render('register',['model'=>$model]);
    }
    //>>>>>>>>>>>>>>>>>>>>>>>>>>登录功能>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
    public function actionLogin(){
        $model=new User();
        if(\yii::$app->request->isPost){
            if($model->load(\yii::$app->request->post())&&$model->validate()){
                //根据名字去数据库查询数据
                $name=User::findOne(['username'=>$model->username]);
                //验证hash密码
                if(\yii::$app->security->validatePassword($model->password,$name->password_hash)){
                    //保存cookies
                    \yii::$app->user->login($name,$model->remember?24*3600:0);
                    $name->last_login_time=time();
                    $name->last_login_ip=\yii::$app->request->userIP;
                    $name->save();
                    //已登录,先看cookie里面有没有添加过商品cart
                    $cookies = \Yii::$app->request->cookies;
                    $cart=unserialize($cookies->getValue('cart'));
                    if($cart){
                        foreach($cart as $key=>$v){
                            $model=Cart::findOne(['goods_id'=>$key,'member_id'=>\yii::$app->user->id]);
                            if($model){
                                $model->amounts+=$v;
                            }else{
                                $new=new Cart();
                                $new->member_id=\yii::$app->user->id;
                                $new->goods_id=$key;
                                $new->amount=$v;
                                $new->save(false);
                            }
                            \yii::$app->response->cookies->remove('cart');

                        }
                    }
                    return $this->redirect(['goods/index']);

                }else{
                    return $this->render('login',['model'=>$model]);
                }
            };
        }
        return $this->render('login',['model'=>$model]);
    }
        //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>地址管理
    public function actionAddress(){
            $model=new ShopAddress();
        if(\yii::$app->request->isPost){
//            echo 11;exit;
            if($model->load(\yii::$app->request->post())&&$model->validate()){
//                echo 111;exit;
               $model->province=Locations::findOne(['id'=>$model->province_id])->name;
                $model->city=Locations::findOne(['id'=>$model->city_id])->name;
                $model->area=Locations::findOne(['id'=>$model->area_id])->name;
                $model->status=$model->status==1?1:0;
                $model->user_id=\yii::$app->user->id;
                $model->save();
            }
        }
        return $this->render('address',['model'=>$model]);
}
    //验证方法
    public function actions(){
        return [
            'captcha'=>[
                'class'=>CaptchaAction::className(),
                'minLength' => 3,
                'maxLength' => 3,
            ]
        ];
    }

    //删除对应的地址
    public function actionAddressDelete($id){
        $address=ShopAddress::findOne(['id'=>$id]);
        $address->delete();
        return $this->redirect(['goods/index']);
    }

    //地址修改功能
    public function actionAddressEdit($id){
        $model=ShopAddress::findOne(['id'=>$id]);
        if($model->load(\yii::$app->request->post())&&$model->validate()){
            //根据id查询到对应的名字
        $model->province=Locations::findOne(['id'=>$model->province_id])->name;
        $model->city=Locations::findOne(['id'=>$model->city_id])->name;
        $model->area=Locations::findOne(['id'=>$model->area_id])->name;
        $model->status=$model->status==1?1:0;
        $model->user_id=\yii::$app->user->id;
        $model->save();
    }
        return $this->render('address',['model'=>$model]);
    }

    //短信发送功能
    public function actionTestSms(){
        $className='Aliyun\Core\Profile\DefaultProfile';
        $classFile=\yii::getAlias('@Aliyun/Core/Profile/DefaultProfile.php');
        Config::load();
        //此处需要替换成自己的AK信息
        $accessKeyId = "LTAIYwqF0MsmFsp8";//参考本文档步骤2
        $accessKeySecret = "eoeJhbXm7p4Vicvk1mD1O9PwnsNGsi";//参考本文档步骤2
        //短信API产品名（短信产品名固定，无需修改）
        $product = "Dysmsapi";
        //短信API产品域名（接口地址固定，无需修改）
        $domain = "dysmsapi.aliyuncs.com";
        //暂时不支持多Region（目前仅支持cn-hangzhou请勿修改）
        $region = "cn-hangzhou";
        //初始化访问的acsCleint
        $profile =DefaultProfile::getProfile($region, $accessKeyId, $accessKeySecret);
        DefaultProfile::addEndpoint("cn-hangzhou", "cn-hangzhou", $product, $domain);
        $acsClient= new DefaultAcsClient($profile);
        $request = new SendSmsRequest();
        //必填-短信接收号码。支持以逗号分隔的形式进行批量调用，批量上限为1000个手机号码,批量调用相对于单条调用及时性稍有延迟,验证码类型的短信推荐使用单条调用的方式
        $tel=\yii::$app->request->get('tel');
        $request->setPhoneNumbers($tel);
        //必填-短信签名
        $request->setSignName("田氏药店");
        //必填-短信模板Code
        $request->setTemplateCode("SMS_80245057");
        //选填-假如模板中存在变量需要替换则为必填(JSON格式),友情提示:如果JSON中需要带换行符,请参照标准的JSON协议对换行符的要求,比如短信内容中包含\r\n的情况在JSON中需要表示成\\r\\n,否则会导致JSON在服务端解析失败
        $code=rand(1000,9999);
        $session=\yii::$app->session;
        $session=[$tel=>$code];
        //保存验证码到sessions中
        $request->setTemplateParam("{\"code\":$code}");
        //选填-发送短信流水号
//        $request->setOutId("1234");
        //发起访问请求
        $acsResponse = $acsClient->getAcsResponse($request);
        return 'success';
    }
    public function actionLogout(){
        \yii::$app->user->logout();
        return $this->redirect(['user/login']);
    }
}
