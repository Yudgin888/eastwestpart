<?php

namespace app\controllers;

use app\models\Category;
use app\models\Users;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\FileHelper;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\UploadForm;
use yii\web\UploadedFile;
use app\myClass\CreateTables;

define('ADMIN', '1');

include(Yii::getAlias('@app/functions.php'));

class SiteController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            return false;
        }
        CreateTables::up();
        return true;
    }

    private function ajaxHandler()
    {
        $post = Yii::$app->request->post();
        if ($post && $post['name'] == 'change-cat') {
            $id = $post['id'];
            $cats = Category::find()->asArray()->where('id_par=' . $id)->all();
            $result = '';
            if (count($cats) > 0) {
                $result .= '<select class="select-item-cat">
                                <option value="Выберите категорию" data-ism="0" data-id="0" selected>Выберите категорию</option>';
                foreach ($cats as $cat) {
                    $name = $cat['num'] . ' ' . $cat['name'];
                    $result .= '<option value="' . $name . '" data-ism="' . $cat['ism'] . '" data-id="' . $cat['id'] . '">' . $name . '</option>';
                }
                $result .= '</select>';
            }
            return $result;
        } else return false;
    }

    public function actionIndex()
    {
        if(Yii::$app->user->isGuest){
            return $this->actionLogin();
        }
        if (Yii::$app->request->isAjax) {
            return $this->ajaxHandler();
        }

        $cats = Category::find()->asArray()->where('id_par=0')->all();
        return $this->render('index', compact('cats'));
    }

    public function actionModel()
    {
        if(Yii::$app->user->isGuest){
            return $this->actionLogin();
        }
        return $this->render('model');
    }

    public function actionSettings()
    {
        if(Yii::$app->user->isGuest || Yii::$app->user->identity->getRole() !== ADMIN){
            return $this->actionIndex();
        }
        $uploadmodel = new UploadForm();
        if (Yii::$app->request->isPost) {
            $uploadmodel->file = UploadedFile::getInstance($uploadmodel, 'file');
            if ($uploadmodel->file && $uploadmodel->validate()) {
                $path = Yii::getAlias('@app/uploads');
                if (!file_exists($path)) {
                    FileHelper::createDirectory($path);
                }
                $filename = $path . '/' . $uploadmodel->file->baseName . '.' . $uploadmodel->file->extension;
                if ($uploadmodel->file->saveAs($filename)) {
                    Yii::$app->session->setFlash('success-load', 'Файл успешно загружен!');
                } else {
                    Yii::$app->session->setFlash('error-load', 'Не удалось загрузить файл!');
                }
                if (loadPrice($filename)) {
                    Yii::$app->session->setFlash('success-proc', 'Файл успешно обработан!');
                } else {
                    Yii::$app->session->setFlash('error-proc', 'Не удалось обработать файл!');
                }
                $uploadmodel = new UploadForm();
                return $this->redirect('/settings');
            }
        }
        $users = Users::find()->asArray()->all();
        return $this->render('settings', compact('uploadmodel', 'users'));
    }

    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goHome();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }
}

