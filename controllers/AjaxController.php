<?php
/**
 * Created by PhpStorm.
 * User: yudgi
 * Date: 04.02.2019
 * Time: 21:50
 */

namespace app\controllers;

use app\models\Agency;
use app\models\Category;
use app\models\Cities;
use app\models\TModel;
use app\models\Users;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class AjaxController extends MainController
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
        if (!parent::beforeAction($action) || Yii::$app->user->isGuest || !Yii::$app->request->isAjax) {
            return false;
        }
        return true;
    }

    public function actionChangecategory()
    {
        $post = Yii::$app->request->post();
        if ($post) {
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

    public function actionDeleteuser()
    {
        $post = Yii::$app->request->post();
        if ($post && Yii::$app->user->identity->getRole() === ADMIN) {
            if ($this->delUserById($post['id'])) {
                Yii::$app->session->setFlash('success-proc', 'Пользователь удален!');
            } else {
                Yii::$app->session->setFlash('error-proc', 'Не удалось удалить пользователя!');
            }
            return $this->redirect('/settings?tab=users');
        } else return false;
    }

    private function delUserById($id)
    {
        $user = Users::find()->where(['id' => $id])->one();
        if ($user) {
            if (!Yii::$app->user->isGuest && Yii::$app->user->identity->getId() != $id) {
                try {
                    return $user->delete();
                } catch (\Throwable $ex) {
                    return false;
                }
            }
        }
        return false;
    }

    public function actionEditmodel()
    {
        $post = Yii::$app->request->post();
        if ($post && Yii::$app->user->identity->getRole() === ADMIN) {
            $model = TModel::find()->where(['id' => $post['id']])->all()[0];
            if ($model) {
                $model->delivery = addslashes(htmlspecialchars($post['txt']));
                try {
                    $model->update();
                    Yii::$app->session->setFlash('success-proc', 'Изменения сохранены!');
                } catch (\Throwable $ex) {
                    Yii::$app->session->setFlash('error-proc', 'Не удалось сохранить изменения!');
                }
            } else {
                Yii::$app->session->setFlash('error-proc', 'Не удалось сохранить изменения!');
            }
            return $this->redirect('/settings?tab=upload-offers');
        } else return false;
    }

    public function actionGetcities()
    {
        $post = Yii::$app->request->post();
        if ($post && !empty($post['query'])) {
            $query = $post['query'];
            $result = Cities::find()->asArray()->where(['like', 'name', $query])->limit(10)->all();
            $result = array_map(function($item){
                return $item['name'];
            }, $result);
            return json_encode($result);
        } else return false;
    }

    public function actionEditagency()
    {
        $post = Yii::$app->request->post();
        if ($post && !empty($post['name']) && Yii::$app->user->identity->getRole() === ADMIN) {
            $agency = Agency::findOne(['id' => $post['id']]);
            if ($agency) {
                $agency->name = $post['name'];
                $agency->update();
                Yii::$app->session->setFlash('success-proc', 'Изменения сохранены!');
                return true;
            } else {
                Yii::$app->session->setFlash('error-proc', 'Не удалось сохранить изменения!');
                return false;
            }
        } else return false;
    }

    public function actionDeleteagency()
    {
        $post = Yii::$app->request->post();
        $res = false;
        if ($post && !empty($post['id']) && Yii::$app->user->identity->getRole() === ADMIN) {
            $agency = Agency::find()->where(['id' => $post['id']])->one();
            if ($agency) {
                try {
                    $res = $agency->delete();
                } catch (\Throwable $ex) {}
            }
        }
        if($res){
            Yii::$app->session->setFlash('error-proc', 'Представительство удалено!');
        } else {
            Yii::$app->session->setFlash('error-proc', 'Не удалось удалить представительство!');
        }
        return $res;
    }
}