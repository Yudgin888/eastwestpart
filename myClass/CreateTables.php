<?php
namespace app\myClass;
use yii\db\Exception;
class CreateTables
{
    public static function up()
    {
        $db = \Yii::$app->getDb();
        $sql = "SET sql_mode=''";
        $db->createCommand($sql)->execute();
        if ($db->getTableSchema('cr_of_category', true) === null) {
            $sql = "
                CREATE TABLE `cr_of_category` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `name` varchar(255) NOT NULL,
                  `num` varchar(255),
                  `info` longtext NOT NULL,
                  `id_par` int(11) DEFAULT 0,
                  `ism` int(11) DEFAULT 0,
                  PRIMARY KEY (`id`)
                );";
            $db->createCommand($sql)->execute();
        }
        if ($db->getTableSchema('cr_of_model', true) === null) {
            $sql = "
                CREATE TABLE `cr_of_model` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `name` varchar(255) NOT NULL,
                  `parameters` longtext,
                  `price` varchar(255),
                  `id_category` int(11),
                  `offer_path` longtext,
                  `delivery` longtext,
                  PRIMARY KEY (`id`)
                );";
            $db->createCommand($sql)->execute();
        }
        if ($db->getTableSchema('cr_of_user', true) === null) {
            $sql = "
                CREATE TABLE `cr_of_user` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `username` varchar(255) NOT NULL,
                  `password` varchar(255) NOT NULL,
                  `auth_key` varchar(255),
                  `role` int(11) NOT NULL,
                  `accessToken` varchar(255),
                  `id_agency` int(11),
                  PRIMARY KEY (`id`)
                );";
            $db->createCommand($sql)->execute();
            $pass = \Yii::$app->getSecurity()->generatePasswordHash('pass');
            $sql = "INSERT INTO `eastwestpart`.`cr_of_user` (`username`, `password`, `auth_key`, `role`, `accessToken`) VALUES ('admin', '{$pass}', '', '1', '');";
            $db->createCommand($sql)->execute();
        }
        if ($db->getTableSchema('cr_of_agency', true) === null) {
            $sql = "
                CREATE TABLE `cr_of_agency` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `name` varchar(255) NOT NULL,
                  `address` longtext,
                  `footer` longtext,
                  PRIMARY KEY (`id`)
                );";
            $db->createCommand($sql)->execute();
        }
        if ($db->getTableSchema('cr_of_option', true) === null) {
            $sql = "
                CREATE TABLE `cr_of_option` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `name` varchar(255) NOT NULL,
                  `cost` varchar(255),
                  `basic` int(11),
                  `id_model` int(11) NOT NULL,
                  PRIMARY KEY (`id`)
                );";
            $db->createCommand($sql)->execute();
        }
        if ($db->getTableSchema('cr_of_settings', true) === null) {
            $sql = "
                CREATE TABLE `cr_of_settings` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `name` varchar(255) NOT NULL,
                  `value` longtext NOT NULL,
                  PRIMARY KEY (`id`)
                );";
            $db->createCommand($sql)->execute();
        }
        if ($db->getTableSchema('cr_of_cities', true) === null) {
            $sql = "
                CREATE TABLE `cr_of_cities` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `name` varchar(255) NOT NULL,
                  PRIMARY KEY (`id`)
                );";
            $db->createCommand($sql)->execute();
        }
        if ($db->getTableSchema('cr_of_logs', true) === null) {
            $sql = "
                CREATE TABLE `cr_of_logs` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `date` varchar(255) NOT NULL,
                  `message` longtext,
                  `status` int(11) DEFAULT 0,
                  PRIMARY KEY (`id`)
                );";
            $db->createCommand($sql)->execute();
        }
    }
//    public static function reset(){
//        self::up();
//        $db = \Yii::$app->getDb();
//        $sql = "DELETE FROM `cr_of_category`";
//        $db->createCommand($sql)->execute();
//        $sql = "DELETE FROM `cr_of_model`";
//        $db->createCommand($sql)->execute();
//        $sql = "DELETE FROM `cr_of_option`";
//        $db->createCommand($sql)->execute();
//    }
//
//    public static function downOptions()
//    {
//        $db = \Yii::$app->db;
//        if ($db->getTableSchema('cr_of_option', true) !== null) {
//            try {
//                $db->createCommand()->dropTable('cr_of_option')->execute();
//            } catch (Exception $e){}
//        }
//    }
}