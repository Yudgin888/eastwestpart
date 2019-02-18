<?php
use yii\bootstrap\ActiveForm;
include 'settings-header.php';
?>

<div class="setting-tab-item users">
    <h2>Пользователи:</h2>
    <div class="container">
        <?php if($users && count($users) > 0):?>
            <table class="table">
                <thead class="thead-light">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Логин</th>
                    <th scope="col">Пароль</th>
                    <th scope="col">Представительство</th>
                    <th scope="col">Группа пользователей</th>
                    <th scope="col">Действия</th>
                </tr>
                </thead>
                <tbody>
                <?php $i = 1; foreach ($users as $item): ?>
                    <tr data-id="<?= $item['id']?>">
                        <td scope="row"><?= $i?></td>
                        <td><?= $item['username']?></td>
                        <td>***********************</td>
                        <?php
                            $agency_list = [];
                            foreach ($agencys as $agency){
                                $agency_list[$agency['id']] = substr($agency['name'], 0, 50);
                            }
                            $ag = null;
                            if(!empty($item['id_agency'])) {
                                $ag = $agency_list[$item['id_agency']];
                            }
                        ?>
                        <td><?= (!empty($ag) ? $ag : 'Представительство не задано')?></td>
                        <td><?= ($item['role'] == 1 ? 'Admin' : ($item['role'] == 2 ? 'Manager' : ''))?></td>
                        <td>
                            <?php if(Yii::$app->user->identity->getId() != $item['id']):?>
                                <div class="div-btn-1">
                                    <button class="btn btn-danger glyphicon glyphicon-remove btn-user-remove"></button>
                                </div>
                            <?php endif;?>
                        </td>
                    </tr>
                    <?php $i++; endforeach;?>
                <tr data-id="new">
                    <?php $form = ActiveForm::begin([
                        'id' => 'adduser-form',
                        'layout' => 'horizontal',
                        'fieldConfig' => [
                            'template' => "{input}{error}",
                            'labelOptions' => ['class' => 'col-5 control-label'],
                        ],
                    ]); ?>
                    <td scope="row">*</td>
                    <td>
                        <?= $form->field($usermodel, 'username')->input('text')->hint('Поле не может быть пустым!'); ?>
                    </td>
                    <td>
                        <?= $form->field($usermodel, 'password')->input('text')->hint('Поле не может быть пустым!'); ?>
                    </td>
                    <td>
                        <?= $form->field($usermodel, 'id_agency')->dropDownList($agency_list);?>
                    </td>
                    <td>
                        <?= $form->field($usermodel, 'role')->dropDownList([
                            '2' => 'Manager',
                            '1' => 'Admin',
                        ]);?>
                    </td>
                    <td>
                        <div class="row">
                            <div class="col">
                                <?= \yii\helpers\Html::submitButton('Добавить пользователя', ['class' => 'btn btn-primary', 'name' => 'add-button', 'id' => 'btn-adduser']) ?>
                            </div>
                        </div>
                    </td>
                    <?php ActiveForm::end(); ?>
                </tr>
                </tbody>
            </table>
        <?php endif;?>

    </div>
</div>
    <div id="dialog" style="display:none" title="Удаление пользователя"></div>

<?php
include 'settings-footer.php';
?>