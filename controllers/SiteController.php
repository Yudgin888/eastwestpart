<?php

namespace app\controllers;

use app\models\Category;
use app\models\Option;
use app\models\Settings;
use app\models\TModel;
use app\models\UploadFormCostFiles;
use app\models\UploadOffers;
use app\models\UserForm;
use app\models\Users;
use app\myClass\PDFHandler;
use Dompdf\Exception;
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
            return $this->redirect('/settings?tab=users');
        } elseif ($post && $post['name'] == 'edit-model') {
            $model = TModel::find()->where(['id' => $post['id']])->all()[0];
            if($model){
                $model->delivery = addslashes(htmlspecialchars($post['txt']));
                $model->update();
                Yii::$app->session->setFlash('success-proc', 'Изменения сохранены!');
            } else {
                Yii::$app->session->setFlash('error-proc', 'Не удалось сохранить изменения!');
            }
            return $this->redirect('/settings?tab=upload-offers');
        }else return false;
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
        $id = Yii::$app->request->get('id');
        if(empty($id)){
            return $this->goBack();
        }
        $model = TModel::find()->asArray()->where('id=' . $id)->all()[0];
        if(empty($model)){
            return $this->goBack();
        }
        $this->view->title = 'Модель ' . $model['name'];
        $options_id = Yii::$app->request->get('opts');
        $city = Yii::$app->request->get('city');
        $cost = Yii::$app->request->get('cost');
        $htmlPage = $this->createOptionsPage($id, $options_id, $city, $cost);
        $pdfhandler_path = Yii::getAlias('@app/pdfhandler_tmp');
        if (!file_exists($pdfhandler_path)) {
            FileHelper::createDirectory($pdfhandler_path);
        }
        $tmp_path = $pdfhandler_path . '/tmp';
        FileHelper::removeDirectory($tmp_path);
        FileHelper::createDirectory($tmp_path);
        $tmp_path .= '/pdf_' . time() . '.pdf';
        $files = [];
        if(!empty($model['offer_path'])){
            $files[] = Yii::getAlias('@app/web/') . $model['offer_path'];
        }
        if($htmlPage) {
            if(PDFHandler::createPDFFile($htmlPage, $tmp_path)) {
                $files[] = $tmp_path;
            }
        }
        if(!empty(trim($model['delivery']))){
            $epilog = $pdfhandler_path . '/tmp/pdf_epilog_' . time() . '.pdf';
            $html = $this->createEpilogPage(htmlspecialchars_decode(stripslashes($model['delivery'])));
            if(PDFHandler::createPDFFile($html, $epilog)) {
                $files[] = $epilog;
            }
        } else {
            $epilog = Settings::find()->asArray()->where(['name' => 'epilog'])->all()[0];
            if (!empty($epilog) && !empty($epilog['value'])) {
                $files[] = Yii::getAlias('@app/web/') . $epilog['value'];
            }
        }
        $pathResult = $pdfhandler_path . '/resultmerge';
        FileHelper::removeDirectory($pathResult);
        FileHelper::createDirectory($pathResult);
        return PDFHandler::mergePDF($files, $pathResult . '/tmp_' . time() . '.pdf');
    }

    private function createEpilogPage($html){
        return $this->renderAjax('epilog-page', compact('html'));
    }

    private function createOptionsPage($id, $options_id, $city, $cost){
        if(!empty($options_id)) {
            $options_id = explode(' ', $options_id);
            $options = Option::find()->asArray()->where([
                'id_model' => $id,
                'id' => $options_id,
            ])->all();
            return $this->renderAjax('viewpdfopt', compact('options', 'city', 'cost'));
        } else {
            $options = Option::find()->asArray()->where('id_model=' . $id)->all();
            if(count($options) == 0){
                return false;
            }
            usort($options, function($arr1, $arr2){
                return $arr2['basic'] - $arr1['basic'];
            });
            return $this->renderAjax('viewpdfnoopt', compact('options', 'city', 'cost'));
        }
    }

    public function actionIndex()
    {
        if(Yii::$app->user->isGuest) {
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
        $breadcrumbs[] = $cats['0']['name'];
        while($cats['0']['id_par'] != 0){
            $cats = Category::find()->asArray()->where('id=' . $cats['0']['id_par'])->all();
            $breadcrumbs[] = $cats['0']['name'];
        }
        return $this->render('model', compact('models', 'breadcrumbs'));
    }

    private function settingTabPrice()
    {
        $uploadmodel = new UploadForm('xlsx');
        if (Yii::$app->request->isPost) {
            $uploadmodel->file = UploadedFile::getInstance($uploadmodel, 'file');
            if ($uploadmodel->file && $uploadmodel->validate()) {
                $path = Yii::getAlias('@app/web/uploads/prices');
                FileHelper::removeDirectory($path);
                FileHelper::createDirectory($path);
                $filename = $path . '/' . $uploadmodel->file->baseName . '_' . time() . '.' . $uploadmodel->file->extension;
                if ($uploadmodel->file->saveAs($filename)) {
                    Yii::$app->session->setFlash('success-load', 'Файл ' . $uploadmodel->file->baseName . '.' . $uploadmodel->file->extension . ' успешно загружен!');
                } else {
                    Yii::$app->session->setFlash('error-load', 'Не удалось загрузить файл: ' . $uploadmodel->file->baseName . '.' . $uploadmodel->file->extension);
                }
                $result = loadPrice($filename);
                if ($result) {
                    Yii::$app->session->setFlash('success-proc', 'Файл успешно обработан! Добавлено: ' . $result['cats_count'] . ' ' . getNumEnding($result['cats_count'], ['категория', 'категории', 'категорий'])
                        . ', ' . $result['mdl_count'] . ' ' . getNumEnding($result['mdl_count'], ['модель', 'модели', 'моделей']));
                } else {
                    Yii::$app->session->setFlash('error-proc', 'Не удалось обработать файл!');
                }
                return $this->redirect('/settings');
            }
        }
        $count_cat = Category::find()->count();
        $count_mod = TModel::find()->count();
        return $this->render('settings-tab-price', compact('uploadmodel', 'count_cat', 'count_mod'));
    }

    private function settingTabUsers()
    {
        $users = Users::find()->asArray()->all();
        $usermodel = new UserForm();
        $post = Yii::$app->request->post();
        if(isset($post['UserForm'])) {
            if ($usermodel->load($post) && $usermodel->registration()) {
                Yii::$app->session->setFlash('success-add-user', 'Пользователь добавлен!');
                return $this->redirect('/settings?tab=users');
            }
        }
        return $this->render('settings-tab-users', compact('users', 'usermodel'));
    }

    private function settingTabUpLoadOptions()
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
            $opt_count = 0;
            foreach ($result['files'] as $item){
                $res = parseCostFile($item['path']);
                if($res['code'] === 'success'){
                    $succ_mess[] = $item['file_name'];
                    $opt_count += $res['mess'];
                } else {
                    $err_mess[] = $item['file_name'];
                }
            }
            if(!empty($err_mess)) {
                Yii::$app->session->setFlash('error-parse-cost', 'Не удалось обработать файлы: ' . implode(', ', $err_mess));
            }
            if(!empty($succ_mess)) {
                Yii::$app->session->setFlash('success-parse-cost', 'Обработанные файлы: ' . implode(', ', $succ_mess));
                Yii::$app->session->setFlash('success-parse-cost-count', 'Добавлено опций: ' . $opt_count);
            }
            return $this->refresh();
        }
        $count_opt = Option::find()->count();
        $count_mod = TModel::find()->count();
        return $this->render('settings-tab-upload-options', compact('model', 'count_opt', 'count_mod'));
    }

    private function settingTabUpLoadOffers()
    {
        $uploadmodel = new UploadForm('pdf');
        $multiupload = new UploadOffers();
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            if(isset($post['UploadFormKM'])) {
                $uploadmodel->file = UploadedFile::getInstance($uploadmodel, 'file');
                if ($uploadmodel->file && $uploadmodel->validate()) {
                    $model_id = $post['UploadFormKM']['hidden1'];
                    $model = TModel::findOne(['id' => $model_id]);
                    if ($model) {
                        $path = Yii::getAlias('@app/web/uploads/offers/' . $model->name);
                        FileHelper::removeDirectory($path);
                        FileHelper::createDirectory($path);
                        $file_name = $uploadmodel->file->baseName . '_' . time() . '.' . $uploadmodel->file->extension;
                        $filepath = $path . '/' . $file_name;
                        if ($uploadmodel->file->saveAs($filepath)) {
                            $model->offer_path = 'uploads/offers/' . $model->name . '/' . $file_name;
                            $model->update();
                            Yii::$app->session->setFlash('success-load', 'Файл ' . $uploadmodel->file->baseName . '.' . $uploadmodel->file->extension . ' успешно загружен!');
                        } else {
                            Yii::$app->session->setFlash('error-load', 'Не удалось загрузить файл: ' . $uploadmodel->file->baseName . '.' . $uploadmodel->file->extension);
                        }
                    } else {
                        Yii::$app->session->setFlash('error-load', 'Модель ' . $model_id . ' не найдена!');
                    }
                    return $this->redirect('/settings?tab=upload-offers');
                } else {
                    Yii::$app->session->setFlash('error-load', 'Не удалось загрузить файл: ' . $uploadmodel->file->baseName . '.' . $uploadmodel->file->extension);
                    return $this->redirect('/settings?tab=upload-offers');
                }
            } else if(isset($post['UploadOffers'])) {
                $multiupload->files = UploadedFile::getInstances($multiupload, 'files');
                $result = $multiupload->upload();
                if (count($result['success']) > 0) {
                    Yii::$app->session->setFlash('success-load', 'Файлы успешно загружены: ' . implode(', ', $result['success']));
                }
                if(count($result['error1']) > 0){
                    Yii::$app->session->setFlash('error-load', 'Не удалось загрузить файлы: ' . implode(', ', $result['error1']));
                }
                if(count($result['error2']) > 0){
                    Yii::$app->session->setFlash('error-load', 'Не были найдены соответствующие модели для файлов: ' . implode(', ', $result['error2']));
                }
                return $this->redirect('/settings?tab=upload-offers');
            }
        }
        $models = TModel::find()->asArray()->all();
        $count_mod = TModel::find()->count();
        return $this->render('settings-tab-upload-offers', compact('models', 'uploadmodel', 'multiupload', 'count_mod'));
    }

    private function settingTabEpilog(){
        $uploadmodel = new UploadForm('pdf');
        if (Yii::$app->request->isPost) {
            $uploadmodel->file = UploadedFile::getInstance($uploadmodel, 'file');
            if ($uploadmodel->file && $uploadmodel->validate()) {
                $path = Yii::getAlias('@app/web/uploads/epilog');
                FileHelper::removeDirectory($path);
                FileHelper::createDirectory($path);
                $name = $uploadmodel->file->baseName . '_' . time() . '.' . $uploadmodel->file->extension;
                $filename = $path . '/' . $name;
                $urlpath = 'uploads/epilog/' . $name;
                if ($uploadmodel->file->saveAs($filename)) {
                    try {
                        $settings = Settings::findOne(['name' => 'epilog']);
                        if ($settings) {
                            $settings->value = $urlpath;
                            $settings->update();
                        } else {
                            $settings = new Settings('epilog', $urlpath);
                            $settings->save();
                        }
                    } catch (Exception $ex){
                        Yii::$app->session->setFlash('error-proc', 'Ошибка записи в базу данных');
                    }
                    Yii::$app->session->setFlash('success-load', 'Файл ' . $uploadmodel->file->baseName . '.' . $uploadmodel->file->extension . ' успешно загружен!');
                } else {
                    Yii::$app->session->setFlash('error-load', 'Не удалось загрузить файл: ' . $uploadmodel->file->baseName . '.' . $uploadmodel->file->extension);
                }
                return $this->redirect('/settings?tab=upload-epilog');
            }
        }
        //$epilog = Settings::find()->asArray()->where(['name' => 'epilog'])->all()[0];
        $epilog = Settings::findOne(['name' => 'epilog']);
        return $this->render('settings-tab-epilog', compact('uploadmodel', 'epilog'));
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
            return $this->settingTabPrice();
        } elseif($act_tab == 'upload-options') {
            return $this->settingTabUpLoadOptions();
        } elseif($act_tab == 'upload-offers') {
            return $this->settingTabUpLoadOffers();
        } elseif($act_tab == 'upload-epilog'){
            return $this->settingTabEpilog();
        } elseif($act_tab == 'users') {
            return $this->settingTabUsers();
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

