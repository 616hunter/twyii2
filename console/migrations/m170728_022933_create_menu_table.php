<?php

use yii\db\Migration;

/**
 * Handles the creation of table `menu`.
 */
class m170728_022933_create_menu_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('menu', [
            'id' => $this->primaryKey(),
            'name'=>$this->string()->comment('菜单名'),
            'url'=>$this->string()->comment('路由地址'),
            'parent_id'=>$this->integer(),
            'sort'=>$this->integer()->comment('排序')
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('menu');
    }
}
