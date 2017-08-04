<?php

namespace backend\models;

use Yii;
use yii\bootstrap\Html;

/**
 * This is the model class for table "goods".
 *
 * @property integer $id
 * @property string $name
 * @property string $sn
 * @property string $logo
 * @property integer $goods_category_id
 * @property integer $brand_id
 * @property string $market_price
 * @property string $shop_price
 * @property integer $stock
 * @property integer $is_on_sale
 * @property integer $status
 * @property integer $sort
 * @property integer $create_time
 * @property integer $view_times
 */
class Goods extends \yii\db\ActiveRecord
{
    public static function statusOption($status){
        $options= [
            -1=>'删除',0=>'隐藏',1=>'正常'
        ];
//        if($hidden_del){
//            unset($options['-1']);
//        }
        return $options[$status];
    }
    public static function statusOptions($del=true){
        $options= [
            -1=>'删除',0=>'隐藏',1=>'正常'
        ];
        if($del){
            unset($options['-1']);
        }
        return $options;
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'goods';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[ 'brand_id', 'stock', 'is_on_sale', 'status', 'sort', 'create_time', 'view_times','goods_category_id'], 'integer'],
            [['market_price', 'shop_price'], 'number'],
            [['name', 'sn', 'logo'], 'string', 'max' => 255],

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '商品名称',
            'sn' => '货号',
            'logo' => 'logo图片',
            'goods_category_id' => '商品分类id',
            'brand_id' => '品牌id',
            'market_price' => '市场价格',
            'shop_price' => '商品价格',
            'stock' => '库存',
            'is_on_sale' => '是否在售',
            'status' => '状态',
            'sort' => '排序',
            'create_time' => '添加时间',
            'view_times' => '浏览次数',
        ];
    }
    /*
    * 商品和相册关系 1对多
    */
    public function getGalleries()
    {
        return $this->hasMany(GoodsGallery::className(),['goods_id'=>'id']);
    }

    /*
     * 获取商品详情
     */
    public function getGoodsIntro()
    {
        return $this->hasOne(GoodsIntro::className(),['goods_id'=>'id']);
    }

    //获取图片轮播数据
    public function getPics()
    {
        $images = [];
        foreach ($this->galleries as $img){
            $images[] =Html::img($img->path);
        }
        return $images;
    }
}
