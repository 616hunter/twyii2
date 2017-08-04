<?php

use yii\db\Migration;

/**
 * Handles the creation of table `goods`.
 */
class m170722_023331_create_goods_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('goods', [
            'id' => $this->primaryKey(),
            'name'=>$this->string()->comment('商品名称'),
            'sn'=>$this->string()->comment('货号'),
            'logo'=>$this->string()->comment('logo图片'),
            'goods_category_id'=>$this->integer()->comment('商品分类id'),
            'brand_id'=>$this->integer()->comment('品牌id'),
            'market_price'=>$this->decimal(10,2)->comment('市场价格'),
            'shop_price'=>$this->decimal(10,2)->comment('商品价格'),
            'stock'=>$this->integer()->comment('库存'),
            'is_on_sale'=>$this->integer()->comment('是否在售'),
            'status'=>$this->smallInteger()->comment('状态'),
            'sort'=>$this->integer()->comment('排序'),
            'create_time'=>$this->integer()->comment('添加时间'),
            'view_times'=>$this->integer()->comment('浏览次数'),

        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('goods');
    }
}
