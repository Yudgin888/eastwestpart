<?php
include 'settings-header.php';
$models = [];
?>
    <div class="setting-tab-item">
        <h2>Опции: (<?= count($options) ?>)</h2>
        <div class="container">
            <?php foreach ($options as $opt):
                if (!isset($models[$opt['id']])) {
                    $models[$opt['id']] = \app\models\TModel::findOne(['id' => $opt['id_model']]);
                }
                ?>
                <div class="model-item" data-id="<?= $opt['id'] ?>">
                    <div class="tab1">
                        <h3>Опция: <p class="opt-name"><?= $opt['name'] ?></p></h3>
                        <?= \yii\helpers\Html::submitButton('', ['class' => 'btn-del-option btn btn-danger glyphicon glyphicon-remove', 'name' => 'del-button', 'title' => 'Удалить опцию']) ?>
                        <?= \yii\helpers\Html::submitButton('', ['class' => 'btn-edit-open-opt btn btn-primary glyphicon glyphicon-pencil', 'name' => 'edit-button', 'title' => 'Редактировать']) ?>
                        <label class="l-item">Модель:
                            <?php if (isset($models[$opt['id']])): ?>
                                <p><?= $models[$opt['id']]->name ?></p>
                            <?php endif; ?>
                        </label>
                        <label class="l-item">Стоимость:
                            <p><?= $opt['cost'] ?></p></label>
                        <?php if ($opt['basic'] == 1): ?>
                            <p>Базовая опция</p>
                        <?php elseif ($opt['basic'] == 0): ?>
                            <p>Дополнительная опция</p>
                        <?php endif; ?>
                    </div>

                    <div class="tab2">

                    </div>
                </div>
                <hr>
            <?php endforeach; ?>
        </div>
    </div>
    <div id="dialog" style="display:none" title="Удаление опции"></div>

<?php
include 'settings-footer.php';
?>