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
use app\models\Logs;
use app\models\Option;
use app\models\TModel;
use app\models\User;
use app\models\Users;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\FileHelper;

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
                $result .= '<select class="select-item-cat form-control">
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

    public function actionEditmodel()
    {
        $post = Yii::$app->request->post();
        if ($post && Yii::$app->user->identity->getRole() === ADMIN) {
            $model = TModel::find()->where(['id' => $post['id']])->all()[0];
            if ($model) {
                $model->delivery = addslashes(htmlspecialchars($post['delivery']));
                $model->name = $post['name'];
                $model->id_category = intval($post['id_category']);
                try {
                    $model->update();
                    Yii::$app->session->setFlash('success-proc', 'Изменения сохранены!');
                } catch (\Throwable $ex) {
                    Yii::$app->session->setFlash('error-proc', 'Не удалось сохранить изменения!');
                }
            } else {
                Yii::$app->session->setFlash('error-proc', 'Не удалось сохранить изменения!');
            }
            return $this->redirect('/settings?tab=models');
        } else return false;
    }

    public function actionGetcities()
    {
        $post = Yii::$app->request->post();
        if ($post && !empty($post['query'])) {
            $query = $post['query'];
            $result = Cities::find()->asArray()->where(['like', 'name', $query])->limit(10)->all();
            $result = array_map(function ($item) {
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
                } catch (\Throwable $ex) {
                }
            }
        }
        if ($res) {
            Yii::$app->session->setFlash('success-proc', 'Представительство удалено!');
        } else {
            Yii::$app->session->setFlash('error-proc', 'Не удалось удалить представительство!');
        }
        return $res;
    }

    public function actionDeleteoption()
    {
        $post = Yii::$app->request->post();
        $res = false;
        if ($post && !empty($post['id']) && Yii::$app->user->identity->getRole() === ADMIN) {
            $option = Option::find()->where(['id' => $post['id']])->one();
            if ($option) {
                try {
                    $res = $option->delete();
                } catch (\Throwable $ex) {
                }
            }
        }
        if ($res) {
            Yii::$app->session->setFlash('success-proc', 'Опция удалена!');
        } else {
            Yii::$app->session->setFlash('error-proc', 'Не удалось удалить опцию!');
        }
        return $res;
    }

    public function actionGeteditoption()
    {
        $post = Yii::$app->request->post();
        $res = false;
        if ($post && !empty($post['id']) && Yii::$app->user->identity->getRole() === ADMIN) {
            $option = Option::find()->where(['id' => $post['id']])->one();
            $models = TModel::find()->asArray()->all();
            $res = "<label>Опция: <input class='input-edit-name form-control' type='text' value='" . $option['name'] . "'></label>
                    <label>Модель:</label>
                    <select class='form-control select-model'>";
                    foreach ($models as $model):
                        $res .= "<option " . ($option['id_model'] == $model['id'] ? 'selected' : '') . " value='" . $model['id'] . "'>" . $model['name'] . "</option>";
                    endforeach;
            $res .= "</select>
                    <label>Стоимость: <input class='input-edit-cost form-control' type='text' value='" . $option['cost'] . "'></label>
                    <label>Тип опции:</label>
                    <select class='form-control select-type'>
                          <option " . ($option['basic'] == 1 ? 'selected' : '') . " value='1'>Базовая</option>
                          <option " . ($option['basic'] == 0 ? 'selected' : '') . " value='0'>Дополнительная</option>
                    </select>" .
                \yii\helpers\Html::submitButton('', ['class' => 'btn-save-option btn btn-success glyphicon glyphicon-ok', 'name' => 'save-button', 'title' => 'Сохранить']) .
                \yii\helpers\Html::submitButton('', ['class' => 'btn-edit-close-option btn btn-danger glyphicon glyphicon-remove', 'name' => 'close-button', 'title' => 'Отмена']);
        }
        return $res;
    }

    public function actionEditoption()
    {
        $post = Yii::$app->request->post();
        if ($post && !empty($post['name']) && Yii::$app->user->identity->getRole() === ADMIN) {
            $opt = Option::findOne(['id' => $post['id']]);
            if ($opt) {
                $opt->name = $post['name'];
                $opt->cost = $post['cost'];
                $opt->id_model = intval($post['model']);
                $opt->basic = intval($post['type']);
                $opt->update();
                Yii::$app->session->setFlash('success-proc', 'Изменения сохранены!');
                return true;
            } else {
                Yii::$app->session->setFlash('error-proc', 'Не удалось сохранить изменения!');
                return false;
            }
        } else return false;
    }

    public function actionEditcategory()
    {
        $post = Yii::$app->request->post();
        if ($post && !empty($post['name']) && Yii::$app->user->identity->getRole() === ADMIN) {
            $cat = Category::findOne(['id' => $post['id']]);
            if ($cat) {
                $cat->name = $post['name'];
                $cat->num = $post['num'];
                $cat->id_par = $post['id_par'];
                $cat->update();
                Yii::$app->session->setFlash('success-proc', 'Изменения сохранены!');
                return true;
            } else {
                Yii::$app->session->setFlash('error-proc', 'Не удалось сохранить изменения!');
                return false;
            }
        } else return false;
    }

    public function actionDeletecategory()
    {
        $post = Yii::$app->request->post();
        $res = false;
        if ($post && !empty($post['id']) && Yii::$app->user->identity->getRole() === ADMIN) {
            $id = $post['id'];
            $category = Category::find()->where(['id' => $id])->one();
            $cat_name = $category->name;
            if ($category) {
                try {
                    $res = $category->delete();
                } catch (\Throwable $ex) {
                }
            }
            if ($post['mode'] === 'save') {
                $models = TModel::find()->where(['id_category' => $id])->all();
                foreach ($models as $model) {
                    $model->id_category = 0;
                    $model->update();
                }
                $categories = Category::find()->where(['id_par' => $id])->all();
                foreach ($categories as $cat) {
                    $cat->id_par = 0;
                    $cat->update();
                }
                Yii::$app->session->setFlash('success-proc', 'Удалена категория: ' . $cat_name);
            }
        }
        return $res;
    }

    public function actionDeletefooter()
    {
        $post = Yii::$app->request->post();
        $res = false;
        if ($post && !empty($post['id']) && Yii::$app->user->identity->getRole() === ADMIN) {
            $agency = Agency::find()->where(['id' => $post['id']])->one();
            if ($agency) {
                try {
                    $path = Yii::getAlias('@app/web/') . $agency->footer;
                    $agency->footer = '';
                    $agency->update();
                    FileHelper::unlink($path);
                } catch (\Throwable $ex) {
                }
            }
        }
        if ($res) {
            Yii::$app->session->setFlash('success-proc', 'Футер удален!');
        } else {
            Yii::$app->session->setFlash('error-proc', 'Не удалось удалить футер!');
        }
        return $res;
    }


    public function actionModelcatremove()
    {
        $res1 = false;
        $res2 = false;
        if (Yii::$app->request->isAjax && Yii::$app->user->identity->getRole() === ADMIN) {
            $res1 = Category::deleteAll();
            $res2 = TModel::deleteAll();
        }
        if ($res1) {
            Yii::$app->session->setFlash('success-proc', 'Все категории удалены!');
        } else {
            Yii::$app->session->setFlash('error-proc', 'Не удалось удалить категории!');
        }
        if ($res2) {
            Yii::$app->session->setFlash('success-load', 'Все модели удалены!');
        } else {
            Yii::$app->session->setFlash('error-load', 'Не удалось удалить модели!');
        }
        return $res1 && $res2;
    }

    public function actionOptionremove()
    {
        $res1 = false;
        if (Yii::$app->request->isAjax && Yii::$app->user->identity->getRole() === ADMIN) {
            $res1 = Option::deleteAll();
        }
        if ($res1) {
            Yii::$app->session->setFlash('success-proc', 'Все опции удалены!');
        } else {
            Yii::$app->session->setFlash('error-proc', 'Не удалось удалить опции!');
        }
        return $res1;
    }

    public function actionDeleteuser()
    {
        $post = Yii::$app->request->post();
        $res = false;
        if ($post && !empty($post['id']) && Yii::$app->user->identity->getRole() === ADMIN && Yii::$app->user->identity->getId() != $post['id']) {
            $user = Users::find()->where(['id' => $post['id']])->one();
            if ($user) {
                try {
                    $res = $user->delete();
                } catch (\Throwable $ex) {
                }
            }
        }
        if ($res) {
            Yii::$app->session->setFlash('success-proc', 'Пользователь удален!');
        } else {
            Yii::$app->session->setFlash('error-proc', 'Не удалось удалить пользователя!');
        }
        return $res;
    }

    public function actionDeletemodel()
    {
        $res1 = false;
        $post = Yii::$app->request->post();
        $model_name = '';
        if (Yii::$app->request->isAjax && Yii::$app->user->identity->getRole() === ADMIN && $post) {
            $model = TModel::find()->where(['id' => $post['id']])->one();
            if ($model) {
                try {
                    $model_name = $model->name;
                    $res1 = $model->delete();
                } catch (\Throwable $ex) {
                }
            }
        }
        if ($res1) {
            Yii::$app->session->setFlash('success-proc', "Модель '{$model_name}' удалена!");
        } else {
            Yii::$app->session->setFlash('error-proc', "Не удалось удалить модель: '{$model_name}'!");
        }
        return $res1;
    }
}