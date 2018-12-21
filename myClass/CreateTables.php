<?php

namespace app\myClass;

use yii\db\Exception;

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
                  `ism` int(11) NOT NULL DEFAULT 0,
                  PRIMARY KEY (`id`)
                );";
            $db->createCommand($sql)->execute();
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
        }
        if ($db->getTableSchema('user', true) === null) {
            $sql = "
                CREATE TABLE `user` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `username` varchar(255) NOT NULL,
                  `password` varchar(255) NOT NULL,
                  `auth_key` varchar(255),
                  `role` int(11) NOT NULL,
                  `accessToken` varchar(255),
                  PRIMARY KEY (`id`)
                );";
            $db->createCommand($sql)->execute();
            $sql = "INSERT INTO `eastwestpart`.`user` (`username`, `password`, `auth_key`, `role`, `accessToken`) VALUES ('admin', 'pass', '', '1', '');";
            $db->createCommand($sql)->execute();
        }
        if ($db->getTableSchema('option', true) === null) {
            $sql = "
                CREATE TABLE `option` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `name` varchar(255) NOT NULL,
                  `cost` varchar(255),
                  `basic` int(11),
                  `id_model` int(11) NOT NULL,
                  PRIMARY KEY (`id`)
                );";
            $db->createCommand($sql)->execute();
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

    public static function downOptions()
    {
        $db = \Yii::$app->db;
        if ($db->getTableSchema('option', true) !== null) {
            try {
                $db->createCommand()->dropTable('option')->execute();
            } catch (Exception $e){}
        }
    }
}