<?php

namespace app\controllers;

use app\models\Agency;
use app\models\AgencyForm;
use app\models\Category;
use app\models\Cities;
use app\models\CityForm;
use app\models\Logs;
use app\models\LogsSearch;
use app\models\ModelForm;
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
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\UploadForm;
use app\models\UploadForm2;
use yii\web\UploadedFile;

include(Yii::getAlias('@app/functions.php'));

class SiteController extends MainController
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
        return true;
    }


    //формирование pdf
    public function actionViewpdf()
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect('/login');
        }
        $id = Yii::$app->request->get('id');
        if (empty($id)) {
            return $this->goBack();
        }

        $id_agency = '';
        if (Yii::$app->user->identity->getRole() === ADMIN) {
            $id_agency = Yii::$app->request->get('id_agency');
        } else {
            $id_agency = Yii::$app->user->identity->id_agency;
        }
        if (empty($id_agency)) {
            Yii::$app->session->setFlash('error-proc', "Для формирования pdf должна быть привязка к представительству!");
            return $this->goBack();
        }
        //блок с шапкой
        $files = [];
        $agency = Agency::findOne(['id' => $id_agency]);
        $header_path = $agency->address;
        $files[] = $header_path;

        //формирование блока с опциями
        $model = TModel::find()->asArray()->where('id=' . $id)->all()[0];
        if (empty($model)) {
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
        if (!file_exists($tmp_path)) {
            FileHelper::createDirectory($tmp_path);
        }
        $tmp_path .= '/pdf_' . time() . '.pdf';

        //блок с общей информацией
        if (!empty($model['offer_path'])) {
            $files[] = Yii::getAlias('@app/web/') . $model['offer_path'];
        }

        //блок с опциями
        if ($htmlPage) {
            if (PDFHandler::createPDFFile($htmlPage, $tmp_path)) {
                $files[] = $tmp_path;
            }
        }

        //формирование блока с доставкой и оплатой
        if (!empty($agency->footer)) {
            $files[] = $agency->footer;
        } elseif (!empty(trim($model['delivery']))) {
            $epilog_path = $pdfhandler_path . '/tmp/pdf_epilog_' . time() . '.pdf';
            $html = $this->createEpilogPage(htmlspecialchars_decode(stripslashes($model['delivery'])));
            if (PDFHandler::createPDFFile($html, $epilog_path)) {
                $files[] = $epilog_path;
            }
        } else {
            $epilog = Settings::find()->asArray()->where(['name' => 'epilog'])->all();
            if (count($epilog) > 0 && !empty($epilog[0]) && !empty($epilog[0]['value'])) {
                $files[] = Yii::getAlias('@app/web/') . $epilog[0]['value'];
            }
        }

        $pathResult = Yii::getAlias('@app/web/uploads/pdf_offers');
        if (!file_exists($pathResult)) {
            FileHelper::createDirectory($pathResult);
        }
        $name_res_pdf = "/{$model['id']}_" . time() . ".pdf";
        $url = '/uploads/pdf_offers' . $name_res_pdf;
        $pathResult = $pathResult . $name_res_pdf;
        $res = PDFHandler::mergePDF($files, $pathResult);
        if ($res) {
            /*Logs::addLog(Yii::$app->user->identity->username . ' сформировал КП: <a href="' . $url . '">' . $url . '</a>', 1);*/
            Logs::addLog(Yii::$app->user->identity->username . ' сформировал КП: ' . $url, 1);
        }
        return $res;
    }

    private function createEpilogPage($html)
    {
        return $this->renderAjax('epilog-page', compact('html'));
    }

    private function createOptionsPage($id, $options_id, $city, $cost)
    {
        if (!empty($options_id)) {
            $options_id = explode(' ', $options_id);
            $options = Option::find()->asArray()->where([
                'id_model' => $id,
                'id' => $options_id,
            ])->all();
            return $this->renderAjax('viewpdfopt', compact('options', 'city', 'cost'));
        } else {
            $options = Option::find()->asArray()->where('id_model=' . $id)->all();
            if (count($options) == 0) {
                return false;
            }
            usort($options, function ($arr1, $arr2) {
                return $arr2['basic'] - $arr1['basic'];
            });
            return $this->renderAjax('viewpdfnoopt', compact('options', 'city', 'cost'));
        }
    }

    public function actionIndex()
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect('/login');
        }
        $cats = Category::find()->asArray()->where('id_par=0')->all();
        return $this->render('index', compact('cats'));
    }

    public function actionModel()
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect('/login');
        }
        $id = Yii::$app->request->get('id');
        $models = TModel::find()->asArray()->where('id_category=' . $id)->with('option')->all();
        $cats = Category::find()->asArray()->where('id=' . $id)->all();
        $breadcrumbs[] = $cats['0']['name'];
        while ($cats['0']['id_par'] != 0) {
            $cats = Category::find()->asArray()->where('id=' . $cats['0']['id_par'])->all();
            $breadcrumbs[] = $cats['0']['name'];
        }
        $cities = Cities::find()->asArray()->all();
        if (!empty($cities)) {
            $cities = array_map(function ($item) {
                return $item['name'];
            }, $cities);
        }
        $agencys = Agency::find()->asArray()->all();
        return $this->render('model', compact('models', 'breadcrumbs', 'cities', 'agencys'));
    }

    private function settingTabCategories()
    {
        $linecategories = Category::find()->asArray()->all();
        $linecategories = sortCategories($linecategories);
        $categories = CreateTree($linecategories);
        return $this->render('settings-tab-categories', compact('categories', 'linecategories'));
    }

    private function settingTabOptions()
    {
        $linecategories = Category::find()->asArray()->all();
        $linecategories = sortCategories($linecategories);
        $options = Option::find()->asArray()->orderBy('id_model')->all();
        return $this->render('settings-tab-options', compact('options', 'linecategories'));
    }

    private function settingTabAgencys()
    {
        $agencyform = new AgencyForm();
        $uploadmodel = new UploadForm('pdf');
        $uploadmodel_footer = new UploadForm2('pdf', true);
        $post = Yii::$app->request->post();
        if ($post && Yii::$app->user->identity->getRole() === ADMIN) {
            $id = isset($_POST['UploadForm']) ? $_POST['UploadForm']['hidden1'] : (isset($_POST['UploadForm2']) ? $_POST['UploadForm2']['hidden1'] : null);
            if (isset($_POST['AgencyForm']) && isset($_POST['AgencyForm']['name'])) {
                $nameAgency = $_POST['AgencyForm']['name'];
                if ($id === 'add-new' && !$agencyform->validateName($nameAgency)) {
                    Yii::$app->session->setFlash('error-proc', "Представительство с таким названием уже существует ({$nameAgency})!");
                    return $this->redirect('/settings?tab=agencys');
                }
            }

            $uploadmodel->file = UploadedFile::getInstance($uploadmodel, 'file');
            $uploadmodel_footer->file = UploadedFile::getInstance($uploadmodel_footer, 'file');

            if (isset($_POST['UploadForm2']) && empty($_POST['UploadForm'])) {
                if ($uploadmodel_footer->file && $uploadmodel_footer->validate()) {
                    $path = Yii::getAlias('@app/web/uploads/footers');
                    if (!is_dir($path)) {
                        FileHelper::createDirectory($path);
                    }
                    $name = $uploadmodel_footer->file->baseName . '_' . time() . '.' . $uploadmodel_footer->file->extension;
                    $filename = $path . '/' . $name;
                    $urlpath_footer = 'uploads/footers/' . $name;
                    if ($uploadmodel_footer->file->saveAs($filename)) {
                        Yii::$app->session->setFlash('success-load-2', 'Файл ' . $uploadmodel_footer->file->baseName . '.' . $uploadmodel_footer->file->extension . ' успешно загружен!');
                        try {
                            $agency = Agency::findOne(['id' => $id]);
                            if ($agency) {
                                $agency->footer = $urlpath_footer;
                                $agency->update();
                                Yii::$app->session->setFlash('success-proc', 'Изменения сохранены!');
                            }
                        } catch (Exception $ex) {
                            Yii::$app->session->setFlash('error-proc', 'Ошибка записи в базу данных');
                        }
                    } else {
                        Yii::$app->session->setFlash('error-load-2', 'Файл ' . $uploadmodel_footer->file->baseName . '.' . $uploadmodel_footer->file->extension . ' не удалось загрузить!');
                    }
                }
            } elseif ($uploadmodel->file && $uploadmodel->validate()) {
                $path = Yii::getAlias('@app/web/uploads/headers');
                if (!is_dir($path)) {
                    FileHelper::createDirectory($path);
                }
                $name = $uploadmodel->file->baseName . '_' . time() . '.' . $uploadmodel->file->extension;
                $filename = $path . '/' . $name;
                $urlpath_header = 'uploads/headers/' . $name;
                $urlpath_footer = '';
                if ($uploadmodel->file->saveAs($filename)) {

                    if ($uploadmodel_footer->file && $uploadmodel_footer->validate()) {
                        $path = Yii::getAlias('@app/web/uploads/footers');
                        if (!is_dir($path)) {
                            FileHelper::createDirectory($path);
                        }
                        $name = $uploadmodel_footer->file->baseName . '_' . time() . '.' . $uploadmodel_footer->file->extension;
                        $filename = $path . '/' . $name;
                        $urlpath_footer = 'uploads/footers/' . $name;
                        if ($uploadmodel_footer->file->saveAs($filename)) {
                            Yii::$app->session->setFlash('success-load-2', 'Файл ' . $uploadmodel_footer->file->baseName . '.' . $uploadmodel_footer->file->extension . ' успешно загружен!');
                        } else {
                            Yii::$app->session->setFlash('error-load-2', 'Файл ' . $uploadmodel_footer->file->baseName . '.' . $uploadmodel_footer->file->extension . ' не удалось загрузить!');
                            $urlpath_footer = null;
                        }
                    }

                    try {
                        $agency = Agency::findOne(['id' => $id]);
                        if ($agency) {
                            if (!empty($nameAgency)) {
                                $agency->name = $nameAgency;
                            }
                            if (!empty($urlpath_header)) {
                                $agency->address = $urlpath_header;
                            }
                            if (!empty($urlpath_footer)) {
                                $agency->footer = $urlpath_footer;
                            }
                            $agency->update();
                            Yii::$app->session->setFlash('success-proc', 'Изменения сохранены!');
                        } else {
                            $agency = new Agency($nameAgency, $urlpath_header, $urlpath_footer);
                            $agency->save();
                            Yii::$app->session->setFlash('success-proc', 'Представительство добавлено!');
                        }
                    } catch (Exception $ex) {
                        Yii::$app->session->setFlash('error-proc', 'Ошибка записи в базу данных');
                    }
                    Yii::$app->session->setFlash('success-load', 'Файл ' . $uploadmodel->file->baseName . '.' . $uploadmodel->file->extension . ' успешно загружен!');

                } else {
                    Yii::$app->session->setFlash('error-proc', 'Не удалось добавить представительство!');
                }
            }
            return $this->redirect('/settings?tab=agencys');
        }
        $agencys = Agency::find()->asArray()->all();
        return $this->render('settings-tab-agency', compact('agencys', 'agencyform', 'uploadmodel', 'uploadmodel_footer'));
    }

    private function settingTabUpload()
    {
        $uploadmodel = new UploadForm('xlsx');
        $uploadmodel_epilog = new UploadForm2('pdf');
        $model = new UploadFormCostFiles();
        if (Yii::$app->request->isPost) {
            $uploadmodel_epilog->file = UploadedFile::getInstance($uploadmodel_epilog, 'file');
            $uploadmodel->file = UploadedFile::getInstance($uploadmodel, 'file');
            if ($uploadmodel->file && $uploadmodel->validate()) {
                $path = Yii::getAlias('@app/web/uploads/prices');
                if (!is_dir($path)) {
                    FileHelper::createDirectory($path);
                }
                $filename = $path . '/' . $uploadmodel->file->baseName . '_' . time() . '.' . $uploadmodel->file->extension;
                if ($uploadmodel->file->saveAs($filename)) {
                    Yii::$app->session->setFlash('success-load', 'Файл ' . $uploadmodel->file->baseName . '.' . $uploadmodel->file->extension . ' успешно загружен!');
                } else {
                    Yii::$app->session->setFlash('error-load', 'Не удалось загрузить файл: ' . $uploadmodel->file->baseName . '.' . $uploadmodel->file->extension);
                }
                $result = loadPrice($filename);
                if ($result) {
                    Yii::$app->session->setFlash('success-proc', 'Файл успешно обработан! Добавлено: ' . $result['cats_add'] . ' ' . getNumEnding($result['cats_add'], ['категория', 'категории', 'категорий'])
                        . ' (обновлено: ' . $result['cats_upd'] . '), ' . $result['mdl_add'] . ' ' . getNumEnding($result['mdl_add'], ['модель', 'модели', 'моделей']) . ' (обновлено: ' . $result['mdl_upd'] . ')');
                } else {
                    Yii::$app->session->setFlash('error-proc', 'Не удалось обработать файл!');
                }
                return $this->redirect('/settings?tab=upload');
            } elseif ($uploadmodel_epilog->file && $uploadmodel_epilog->validate()) {
                if ($uploadmodel_epilog->file && $uploadmodel_epilog->validate()) {
                    $path = Yii::getAlias('@app/web/uploads/epilog');
                    if (!is_dir($path)) {
                        FileHelper::createDirectory($path);
                    }
                    $name = $uploadmodel_epilog->file->baseName . '_' . time() . '.' . $uploadmodel_epilog->file->extension;
                    $filename = $path . '/' . $name;
                    $urlpath = 'uploads/epilog/' . $name;
                    if ($uploadmodel_epilog->file->saveAs($filename)) {
                        try {
                            $settings = Settings::findOne(['name' => 'epilog']);
                            if ($settings) {
                                $settings->value = $urlpath;
                                $settings->update();
                            } else {
                                $settings = new Settings('epilog', $urlpath);
                                $settings->save();
                            }
                        } catch (Exception $ex) {
                            Yii::$app->session->setFlash('error-proc', 'Ошибка записи в базу данных');
                        }
                        Yii::$app->session->setFlash('success-load', 'Файл ' . $uploadmodel_epilog->file->baseName . '.' . $uploadmodel_epilog->file->extension . ' успешно загружен!');
                    } else {
                        Yii::$app->session->setFlash('error-load', 'Не удалось загрузить файл: ' . $uploadmodel_epilog->file->baseName . '.' . $uploadmodel_epilog->file->extension);
                    }
                    return $this->redirect('/settings?tab=upload');
                }
            } else {
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
                foreach ($result['files'] as $item) {
                    $res = parseCostFile($item['path']);
                    if ($res['code'] === 'success') {
                        $succ_mess[] = $item['file_name'];
                        $opt_count += $res['mess'];
                    } else {
                        $err_mess[] = $item['file_name'];
                    }
                }
                if (!empty($err_mess)) {
                    Yii::$app->session->setFlash('error-parse-cost', 'Не удалось обработать файлы: ' . implode(', ', $err_mess));
                }
                if (!empty($succ_mess)) {
                    Yii::$app->session->setFlash('success-parse-cost', 'Обработанные файлы: ' . implode(', ', $succ_mess));
                    Yii::$app->session->setFlash('success-parse-cost-count', 'Добавлено опций: ' . $opt_count);
                }
                return $this->redirect('/settings?tab=upload');
            }
        }
        $count_opt = Option::find()->count();
        $count_cat = Category::find()->count();
        $count_mod = TModel::find()->count();
        $epilog = Settings::findOne(['name' => 'epilog']);
        return $this->render('settings-tab-upload', compact('uploadmodel', 'model', 'count_cat', 'count_mod', 'count_opt', 'uploadmodel_epilog', 'epilog'));
    }

    private function settingTabUsers()
    {
        $users = Users::find()->asArray()->all();
        $usermodel = new UserForm();
        $post = Yii::$app->request->post();
        if (isset($post['UserForm']) && Yii::$app->user->identity->getRole() === ADMIN) {
            if ($usermodel->load($post) && $usermodel->registration()) {
                Yii::$app->session->setFlash('success-proc', 'Пользователь добавлен!');
            } else {
                $err = array_values($usermodel->errors);
                if (isset($err[0][0])) {
                    Yii::$app->session->setFlash('error-proc', $err[0][0]);
                } else {
                    Yii::$app->session->setFlash('error-proc', 'Не удалось добавить пользователя!');
                }
            }
            return $this->redirect('/settings?tab=users');
        }
        $agencys = Agency::find()->asArray()->all();
        return $this->render('settings-tab-users', compact('users', 'usermodel', 'agencys'));
    }

    private function settingTabModels()
    {
        $uploadmodel = new UploadForm('pdf');
        $multiupload = new UploadOffers();
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            if (isset($post['UploadForm'])) {
                $uploadmodel->file = UploadedFile::getInstance($uploadmodel, 'file');
                if ($uploadmodel->file && $uploadmodel->validate()) {
                    $model_id = $post['UploadForm']['hidden1'];
                    $model = TModel::findOne(['id' => $model_id]);
                    if ($model) {
                        $path = Yii::getAlias('@app/web/uploads/offers/' . $model->name);
                        if (!is_dir($path)) {
                            FileHelper::createDirectory($path);
                        }
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
                } else {
                    Yii::$app->session->setFlash('error-load', 'Не удалось загрузить файл: ' . $uploadmodel->file->baseName . '.' . $uploadmodel->file->extension);
                }
                return $this->redirect('/settings?tab=models');
            } else if (isset($post['UploadOffers'])) {
                $multiupload->files = UploadedFile::getInstances($multiupload, 'files');
                $result = $multiupload->upload();
                if (count($result['success']) > 0) {
                    Yii::$app->session->setFlash('success-load', 'Файлы успешно загружены: ' . implode(', ', $result['success']));
                }
                if (count($result['error1']) > 0) {
                    Yii::$app->session->setFlash('error-load', 'Не удалось загрузить файлы: ' . implode(', ', $result['error1']));
                }
                if (count($result['error2']) > 0) {
                    Yii::$app->session->setFlash('error-load', 'Не были найдены соответствующие модели для файлов: ' . implode(', ', $result['error2']));
                }
                return $this->redirect('/settings?tab=models');
            }
        }
        $models = TModel::find()->asArray()->all();
        $count_mod = TModel::find()->count();
        $linecategories = Category::find()->asArray()->all();
        $linecategories = sortCategories($linecategories);
        return $this->render('settings-tab-models', compact('models', 'uploadmodel', 'multiupload', 'count_mod', 'linecategories'));
    }

    private function settingTabCities()
    {
        $model = new CityForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                Yii::$app->session->setFlash('success-proc', 'Данные сохранены!');
            } else {
                Yii::$app->session->setFlash('error-proc', 'Не удалось сохранить данные!');
            }
            return $this->redirect('/settings?tab=cities');
        }
        $cities = Cities::find()->asArray()->all();
        if (!empty($cities)) {
            $cities = array_map(function ($item) {
                return $item['name'];
            }, $cities);
            $model->text = implode(', ', $cities);
        }
        return $this->render('settings-tab-cities', compact('model'));
    }

    private function settingTabLogs()
    {
        $searchModel = new LogsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $logs = Logs::find()->asArray()->all();
        return $this->render('settings-tab-logs', compact('logs', 'dataProvider'));
    }

    public function actionSettings()
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect('/login');
        } elseif (Yii::$app->user->identity->getRole() !== ADMIN) {
            return $this->redirect('/');
        }
        $act_tab = Yii::$app->request->get('tab');
        if (empty($act_tab)) {
            $act_tab = 'agencys';
        }
        if ($act_tab == 'agencys') {
            return $this->settingTabAgencys();
        } elseif ($act_tab == 'categories') {
            return $this->settingTabCategories();
        } elseif ($act_tab == 'upload') {
            return $this->settingTabUpload();
        } elseif ($act_tab == 'models') {
            return $this->settingTabModels();
        } elseif ($act_tab == 'options') {
            return $this->settingTabOptions();
        } elseif ($act_tab == 'users') {
            return $this->settingTabUsers();
        } elseif ($act_tab == 'cities') {
            return $this->settingTabCities();
        } elseif ($act_tab == 'logs') {
            return $this->settingTabLogs();
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