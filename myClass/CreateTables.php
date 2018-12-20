<?php

namespace app\myClass;

class CreateTables
{
    public static function up()
    {
        $db = \Yii::$app->getDb();
        //$db->createCommand("SET GLOBAL sql_mode=''");
        if ($db->getTableSchema('category', true) === null) {
            $sql = "
                CREATE TABLE `category` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `name` varchar(255) NOT NULL,
                  `num` varchar(255) NOT NULL,
                  `info` longtext NOT NULL,
                  `id_par` int(11) NOT NULL DEFAULT 0,
                  PRIMARY KEY (`id`)
                );";
            $db->createCommand($sql)->execute();

            /*$db->createCommand()->createTable('category', [
                'id' => $this->primaryKey(),
                'name' => $this->string()->notNull(),
                'info' => $this->text(),
                'id_par' => $this->integer()->notNull()->defaultValue(0),
            ]);*/

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
            $sql = "
                CREATE TABLE `model` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `name` varchar(255) NOT NULL,
                  `parameters` longtext,
                  `price` varchar(255),
                  `id_category` int(11) NOT NULL,
                  PRIMARY KEY (`id`)
                );";
            $db->createCommand($sql)->execute();

            /*$db->createCommand()->createTable('model', [
                'id' => $db->createCommand()->primaryKey(),
                'name' => $this->string()->notNull(),
                'parameters' => $this->text(),
                'price' => $this->string(),
                'id_category' => $this->integer()->notNull(),
            ]);

            $db->createCommand()->addForeignKey(
                "fk_model_category_id",
                "model",
                "id_category",
                "category",
                "id",
                "RESTRICT"
            );*/
        }
    }

    public static function reset(){
        self::up();
        $db = \Yii::$app->getDb();
        $sql = "DELETE FROM `category`";
        $db->createCommand($sql)->execute();
        $sql = "DELETE FROM `model`";
        $db->createCommand($sql)->execute();
    }

    public static function down()
    {
        $db = \Yii::$app->db;
        if ($db->getTableSchema('model', true) !== null) {
            try {
                $db->createCommand()->dropTable('model')->execute();
            } catch (Exception $e){}
        }
        if ($db->getTableSchema('category', true) !== null) {
            try {
                $db->createCommand()->dropTable('category')->execute();
            } catch (Exception $e){}
        }
    }
}