<?php
use yii\db\Migration;

class CreateTables extends Migration
{
    public function up()
    {
        $db = \Yii::$app->db;
        if ($db->getTableSchema('category', true) === null) {
            $this->createTable('category', [
                'id' => $this->primaryKey(),
                'name' => $this->string()->notNull(),
                'info' => $this->string(),
                'id_par' => $this->integer()->defaultValue(-1),
            ]);

            $this->addForeignKey(
                "fk_category_id",
                "category",
                "id_par",
                "category",
                "id",
                "RESTRICT",
                "CASCADE"
            );
        }
        if ($db->getTableSchema('model', true) === null) {
            $this->createTable('model', [
                'id' => $this->primaryKey(),
                'name' => $this->string()->notNull(),
                'parameters' => $this->string(),
                'price' => $this->string(),
                'id_category' => $this->integer()->notNull(),
            ]);

            $this->addForeignKey(
                "fk_model_category_id",
                "model",
                "id_category",
                "category",
                "id",
                "RESTRICT",
                "CASCADE"
            );
        }
    }

    public function down()
    {
        $this->dropTable('category');
        $this->dropTable('model');
    }
}