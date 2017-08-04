<?php
namespace frontend\controllers;
use app\models\Cart;
use app\models\Order;
use app\models\OrderGoods;
use app\models\ShopAddress;
use backend\models\Goods;
use yii\db\Exception;
use yii\web\Controller;
use yii\web\Cookie;

class CarController extends Controller{
    public $enableCsrfValidation=false;
    public $layout=false;
    //商品保存功能
    public function actionAddCar($goods_id,$amount){
        //判断是否登录，登录就保存到数据库，没有登录就保存到cooki中
        if(\yii::$app->user->isGuest){
            $cookies = \yii::$app->request->cookies;
            //获取cookie中的购物车数据
            $cart = $cookies->get('cart');
            if($cart==null){
                $carts = [$goods_id=>$amount];
            }else{
                $carts = unserialize($cart->value);//[1=>99，2=》1]
                if(isset($carts[$goods_id])){
                    //购物车中已经有该商品，数量累加
                    $carts[$goods_id] += $amount;
                }else{
                    //购物车中没有该商品
                    $carts[$goods_id] = $amount;
                }
            }
            //将商品id和商品数量写入cookie
            $cookies = \yii::$app->response->cookies;
            $cookie = new Cookie([
                'name'=>'cart',
                'value'=>serialize($carts),
                //设置cookies的保存时间，必须加上当前的时间戳
                'expire'=>7*24*3600+time()
            ]);
            $cookies->add($cookie);
        }else{
            //用户已登录，操作购物车数据表
            $num=Cart::findOne(['goods_id'=>$goods_id,'member_id'=>\yii::$app->user->id]);
            //判断之前是否有添加相同的商品,有就累加,没有就创建新的
            if($num){
                $num->member_id=\yii::$app->user->id;
                $num->goods_id=$goods_id;
                $num->amounts=$num->amounts+$amount;
                $num->save();
            }else{
                $model=new Cart();
                $model->goods_id=$goods_id;
                $model->member_id=\yii::$app->user->id;
                $model->amounts=$amount;
                var_dump($model->goods_id,$model->member_id);
//                var_dump($model->getErrors());exit;
                $model->save();
            }
        }

        return $this->redirect(['car']);
    }
    //购物车展示功能
    public function actionCar(){
        //这里先判断是否登录，登录直接查询数据库把购物车信息显示到页面，没有登录就取出cookies中的信息
        if(\yii::$app->user->isGuest){
            $cookies=\yii::$app->request ->cookies;
            $cart=$cookies->get('cart');
            //判断$cart是否有值，没有就添加，有就加数量
            if($cart==null){
                $carts=[];
            }else{
                $carts=unserialize($cart->value);
            }
            //$model为所有的商品信息
            $models=Goods::find()->where(['in','id',array_keys($carts)])->asArray()->all()?Goods::find()->where(['in','id',array_keys($carts)])->asArray()->all():[];
            return $this->render('flow1',['models'=>$models,'carts'=>$carts]);
        }else{
            //登录状态下，查询数据库将数据库的数据读取显示出来
            //查询到所有的商品信息
            $carts=Cart::find()->where(['member_id'=>\yii::$app->user->id])->asArray()->all();
//            var_dump($carts);exit;
            return $this->render('flow11',['carts'=>$carts]);

        }

    }

    //实时修改购物车的数据
    public function actionAjaxCart(){
        $goods_id = \yii::$app->request->post('goods_id');
        $amount = \yii::$app->request->post('amount');
        //数据验证
        if(\yii::$app->user->isGuest){
            $cookies = \yii::$app->request->cookies;
            //获取cookie中的购物车数据
            $cart = $cookies->get('cart');
            if($cart==null){
                $carts = [$goods_id=>$amount];
            }else{
                $carts = unserialize($cart->value);//[1=>99，2=》1]
                if(isset($carts[$goods_id])){
                    //购物车中已经有该商品，更新数量
                    $carts[$goods_id] = $amount;
                }else{
                    //购物车中没有该商品
                    $carts[$goods_id] = $amount;
                }
            }
            //将商品id和商品数量写入cookie
            $cookies = \yii::$app->response->cookies;
            $cookie = new Cookie([
                'name'=>'cart',
                'value'=>serialize($carts),
                'expire'=>7*24*3600+time()
            ]);
            $cookies->add($cookie);
            return 'success';
        }else{
            //根据传过来的goods_id和用户ID查询到对应的数据，再进行修改
            $num=Cart::find()->where('goods_id='.$goods_id.' and member_id='.\yii::$app->user->id)->one();
            $num->amounts=$amount;
            $num->save();
        }
    }
    public function actionDelete($id){
        $model=Cart::findOne(['id'=>$id]);
        $model->delete();

        return $this->redirect('car');
    }
    //显示支付页面
    public function actionPay(){
        $model=new Order();
        $order=new OrderGoods();
        //先判断是否登陆，登录就读取购物车数据表，没有登录就跳转到登录页面
        if(\yii::$app->user->isGuest){
            return $this->redirect('/user/login');
        }else{
            $transaction=\Yii::$app->db->beginTransaction();
                if($model->load(\yii::$app->request->post())&&$model->validate()){
                    //开启事务
             try{
                    //处理数据
                 $delivery=new Order();
                 $deliveries=Order::$send;
                 $pay=$delivery::$pay;
                 //根据现在的用户数据查询到购物车的数据
                 $model->member_id=\yii::$app->user->id;
                 $address=ShopAddress::findOne(['id'=>$model->address_id]);
                 $model->name=$address->name;
                 $model->province=$address->province;
                 $model->city=$address->city;
                 $model->area=$address->area;
                 $model->address=$address->address;
                 $model->tel=$address->tel;
                 $model->delivery_name=$deliveries[$model->deliveries_id]['func'];
                 $model->delivery_price=$deliveries[$model->deliveries_id]['price'];
                 $model->delivery_id=$model->deliveries_id;
                 $model->payment_id=$model->pay_id;
                 $model->payment_name=$pay[$model->pay_id]['func'];
                 $model->trade_no=rand(10000,99999);
                 $model->create_time=time();
                 $model->total=$model->total_price;
                 //判断状态，pay_id为1、3时就为代付款
                 if($model->pay_id==1 || $model->pay_id==3||$model->pay_id==4){
                     $model->status=1;
                 }elseif($model->pay_id==2){
                     $model->status=2;
                 }
                 $model->save();
                    //操作order_goods表

                    //取出所有的数据，进行遍历
                $cart=Cart::find()->where(['member_id'=>\yii::$app->user->id])->all();
                    foreach($cart as $v){
                        $order=new OrderGoods();
                        $order->order_id=$model->id;
                        $order->goods_id=$v->goods_id;
                        $order->goods_name=Goods::findOne(['id'=>$v->goods_id])->name;
                        $order->logo=Goods::findOne(['id'=>$v->goods_id])->logo;
                        $order->price=Goods::findOne(['id'=>$v->goods_id])->shop_price;
                        //判断数据库商品数量，如果数量足够就减去对应的数量，如果没有就回滚
                        $goods=Goods::findOne(['id'=>$v->goods_id]);
                        $order->amount=$v->amounts;
                        $cart=Cart::findOne(['goods_id'=>$v->goods_id,'member_id'=>\yii::$app->user->id]);
                        $cart->delete();
                        $order->total=$v->amounts*$order->price;
                        $order->save();
                        //判断大小
                        if($v->amounts < $goods->stock){
                            $goods->stock=($goods->stock)-($v->amounts);
                            $goods->save();
                        }elseif($v->amounts > ($goods->stock)){
                            throw new Exception('商品库存不足，无法继续下单，请修改购物车商品数量');
                        }

                    }
                 $transaction->commit();
                 return $this->redirect('end');
              } catch (Exception $e){
                 $transaction->rollBack();
                 return $this->redirect('pay');
                }
            }else{
                $address=ShopAddress::find()->where(['user_id'=>\yii::$app->user->id])->all();
                $cars=Cart::find()->where(['member_id'=>\yii::$app->user->id])->all();
                    return $this->render('flow2',['model'=>$model,'cars'=>$cars,'address'=>$address]);
                }
            }
        }
    //购物最后一步
    public function actionEnd(){
        return $this->render('flow3');
    }
    //查看订单信息
    public function actionOrdered(){
        //查询出订单表
        $model=Order::find()->where(['member_id'=>\yii::$app->user->id])->all();
        return $this->render('ordered',['models'=>$model]);
    }
    //订单删除表
    public function actionDeleteOrder($id){
    $model=OrderGoods::findOne(['id'=>$id]);
        $model->delete();
        return $this->redirect('ordered');
}
}