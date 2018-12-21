<?php

namespace app\controllers;

use app\models\Category;
use app\models\TModel;
use app\models\UploadFormCostFiles;
use app\models\UserForm;
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
define('MANAGER', '2');

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
        } elseif ($post && $post['name'] == 'delete-user') {
            if($this->delUserById($post['id'])){
                Yii::$app->session->setFlash('success-del-user', 'Пользователь удален!');
            } else {
                Yii::$app->session->setFlash('error-del-user', 'Не удалось удалить пользователя!');
            }
            return $this->refresh();
        } else return false;
    }

    private function delUserById($id)
    {
        $user = Users::find()->where(['id' => $id])->one();
        if($user) {
            if (!Yii::$app->user->isGuest && Yii::$app->user->identity->getId() != $id) {
                return $user->delete();
            }
        }
        return false;
    }

    public function actionViewpdf(){
        if(Yii::$app->user->isGuest){
            return $this->redirect('/login');
        }
        if(empty(Yii::$app->request->get('id'))){
            return $this->goBack();
        }
        $id = Yii::$app->request->get('id');
        return $this->render('viewpdf', compact('id'));
    }

    public function actionIndex()
    {
        if(Yii::$app->user->isGuest){
            return $this->redirect('/login');
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
            return $this->redirect('/login');
        }
        $id = Yii::$app->request->get('id');
        $models = TModel::find()->asArray()->where('id_category=' . $id)->with('option')->all();
        $cats = Category::find()->asArray()->where('id=' . $id)->all();
        $breadcrumbs[] = $title = $cats['0']['name'];
        while($cats['0']['id_par'] != 0){
            $cats = Category::find()->asArray()->where('id=' . $cats['0']['id_par'])->all();
            $breadcrumbs[] = $cats['0']['name'];
        }
        return $this->render('model', compact('models', 'breadcrumbs'));
    }

    private function settingTab1()
    {
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
        return $this->render('settings-tab1', compact('uploadmodel'));
    }

    private function settingTab2()
    {
        $users = Users::find()->asArray()->all();
        $usermodel = new UserForm();
        $post = Yii::$app->request->post();
        if(isset($post['UserForm'])) {
            if ($usermodel->load($post) && $usermodel->registration()) {
                Yii::$app->session->setFlash('success-add-user', 'Пользователь добавлен!');
                return $this->refresh();
            }
        }
        return $this->render('settings-tab2', compact('users', 'usermodel'));
    }

    private function settingTab3()
    {
        $model = new UploadFormCostFiles();
        if (Yii::$app->request->isPost) {
            $model->files = UploadedFile::getInstances($model, 'files');
            $result = $model->upload();
            if ($result['code'] == 'success') {
                Yii::$app->session->setFlash('success-load', 'Файлы успешно загружены!');
            } else {
                $mess = implode(', ', $result['files']);
                Yii::$app->session->setFlash('error-load', 'Не удалось загрузить файлы: ' . $mess);
            }
            $err_mess = [];
            $succ_mess = [];
            foreach ($model->files as $item){
                $path = \Yii::getAlias('@app/uploads/costfiles/' . $item->name);
                $res = parseCostFile($path);
                if($res !== true){
                    $err_mess[] = $item->name;
                } else {
                    $succ_mess[] = $item->name;
                }
            }
            if(!empty($err_mess)) {
                Yii::$app->session->setFlash('error-parse-cost', 'Не удалось обработать файлы: ' . implode(', ', $err_mess));
            }
            if(!empty($succ_mess)) {
                Yii::$app->session->setFlash('success-parse-cost', 'Обработанные файлы: ' . implode(', ', $succ_mess));
            }
            $model = new UploadFormCostFiles();
            return $this->refresh();
        }
        return $this->render('settings-tab3', compact('model'));
    }

    public function actionSettings()
    {
        if(Yii::$app->user->isGuest){
            return $this->redirect('/login');
        } elseif(Yii::$app->user->identity->getRole() !== ADMIN){
            return $this->redirect('/');
        }
        if (Yii::$app->request->isAjax) {
            return $this->ajaxHandler();
        }

        $act_tab = Yii::$app->request->get('tab');
        if(empty($act_tab)){
            $act_tab = 'upload-price';
        }
        if($act_tab == 'upload-price'){
            return $this->settingTab1();
        } elseif($act_tab == 'users'){
            return $this->settingTab2();
        } elseif($act_tab == 'upload-cost'){
            return $this->settingTab3();
        } else {
            return $this->goHome();
        }
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

