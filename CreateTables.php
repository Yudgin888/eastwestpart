<?php
use yii\db\Migration;

class CreateTables extends Migration
{
    public function up()
    {
        $db = \Yii::$app->db;
        $db->createCommand("SET GLOBAL sql_mode=''");
        if ($db->getTableSchema('category', true) === null) {
            $this->createTable('category', [
                'id' => $this->primaryKey(),
                'name' => $this->string()->notNull(),
                'info' => $this->text(),
                'id_par' => $this->integer()->notNull()->defaultValue(0),
            ]);

            /*$this->addForeignKey(
                "fk_category_id",
                "category",
                "id_par",
                "category",
                "id",
                "RESTRICT"
            );*/
        }
        if ($db->getTableSchema('model', true) === null) {
            $this->createTable('model', [
                'id' => $this->primaryKey(),
                'name' => $this->string()->notNull(),
                'parameters' => $this->text(),
                'price' => $this->string(),
                'id_category' => $this->integer()->notNull(),
            ]);

            $this->addForeignKey(
                "fk_model_category_id",
                "model",
                "id_category",
                "category",
                "id",
                "RESTRICT"
            );
        }
    }

    public function down()
    {
        $db = \Yii::$app->db;
        if ($db->getTableSchema('model', true) !== null) {
            $this->dropTable('model');
        }
        if ($db->getTableSchema('category', true) !== null) {
            $this->dropTable('category');
        }
    }
}