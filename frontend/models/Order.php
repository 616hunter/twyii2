<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "order".
 *
 * @property integer $id
 * @property integer $member_id
 * @property string $name
 * @property string $province
 * @property string $city
 * @property string $area
 * @property string $address
 * @property string $tel
 * @property integer $delivery_id
 * @property string $delivery_name
 * @property string $delivery_price
 * @property integer $payment_id
 * @property string $payment_name
 * @property string $total
 * @property string $trade_no
 * @property integer $create_time
 */
class Order extends \yii\db\ActiveRecord{
    public $address_id;
    public $deliveries_id;
    public $pay_id;
    public $total_price;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
        ['total_price','safe'],
            [['id', 'member_id','pay_id', 'deliveries_id', 'payment_id', 'create_time','address_id'], 'integer'],
            [['delivery_price', 'total'], 'number'],
            [['name', 'province', 'city', 'area', 'address', 'delivery_name', 'payment_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'member_id' => 'Member ID',
            'name' => 'Name',
            'province' => 'Province',
            'city' => 'City',
            'area' => 'Area',
            'address' => 'Address',
            'tel' => 'Tel',
            'delivery_id' => 'Delivery ID',
            'delivery_name' => 'Delivery Name',
            'delivery_price' => 'Delivery Price',
            'payment_id' => 'Payment ID',
            'payment_name' => 'Payment Name',
            'total' => 'Total',
            'trade_no' => 'Trade No',
            'create_time' => 'Create Time',
        ];
    }
    public static $send=[
            1=>[ 'id'=>1,'func'=>'申通快递','price'=>'10','detail'=>'传统快递公司，速度一般，服务一般'],
            2=>['id'=>2,'func'=>'顺风快递','price'=>'40','detail'=>'快递行业的龙头,速度快，服务好，价格贵'],
            3=>['id'=>3,'func'=>'邮政快递','price'=>'15','detail'=>'中国最早的快递，速度一般，服务一般，到达率高']
        ];

    public static $pay =[
            1=>['id'=>1,'func'=>'货到付款' ,'detail'=>'送货上门后再收款，支持现金，pos机'],
            2=>['id'=>2,'func'=> '在线支付','detail'=>'即时到账，支持所有的银行卡'],
            3=>['id'=>3,'func'=>'上门自提','detail'=>'自提时付款，支持现金，POS机刷卡、支票支付'],
            4=>['id'=>4,'func'=> '邮局汇款','detail'=>'通过平台收款，汇款后1-3个工作日到账']
        ];
    public static $status=[
        0=>'已取消',
        1=>'代付款',
        2=>'代发货',
        3=>'待收货',
        4=>'完成',
    ];

}
