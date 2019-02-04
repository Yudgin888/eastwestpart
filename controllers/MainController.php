<?php
/**
 * Created by PhpStorm.
 * User: yudgi
 * Date: 04.02.2019
 * Time: 21:52
 */

namespace app\controllers;

use app\myClass\CreateTables;
use yii\web\Controller;

define('ADMIN', '1');
define('MANAGER', '2');

class MainController extends Controller
{
    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            return false;
        }
        CreateTables::up();
        return true;
    }
}