<?php

use yii\bootstrap\ActiveForm;

include 'settings-header.php';
?>

<div class="setting-tab-item">
    <h2>Категории (<?= count($categories) ?>):</h2>

    <div class="container">
        <?php if (count($categories) == 0): ?>
            <p>Не найдено ни одной категории!</p>
        <?php else: ?>
            <?php foreach ($categories as $category): ?>
                <hr>
                <div class="model-item agency-block" data-id="<?= $category['id'] ?>">
                    <div class="agency-name">
                        <div class="tab1">
                            <p class="title"><?= $category['num'] . ' ' . $category['name'] . ($category['ism'] ? ' <span title="Есть модели, привязанные к данной категории">(м)</span>' : '') ?></p>
                        </div>
                        <div class="tab2">
                            <input class='input-edit-num' type="text" value="<?= $category['num'] ?>">
                            <input class='input-edit-name' type="text" value="<?= $category['name'] ?>">
                            <label>Родительская категория: </label>
                            <select>
                                <?php foreach ($linecategories as $cat):?>
                                <option <?= ($category['id_par'] == $cat['id'] ? 'selected' : '')?> value="<?= $cat['id']?>"><?= $cat['num'] . ' ' . $cat['name']?></option>
                                <?php endforeach;?>
                            </select>

                            <?= \yii\helpers\Html::submitButton('', ['class' => 'btn-save-category btn btn-success glyphicon glyphicon-ok', 'name' => 'save-button', 'title' => 'Сохранить']) ?>
                            <?= \yii\helpers\Html::submitButton('', ['class' => 'btn-edit-close btn btn-danger glyphicon glyphicon-remove', 'name' => 'close-button', 'title' => 'Отмена']) ?>
                        </div>
                    </div>
                    <?= \yii\helpers\Html::submitButton('', ['class' => 'btn-del-category btn btn-danger glyphicon glyphicon-remove', 'name' => 'del-button', 'title' => 'Удалить категорию']) ?>
                    <?= \yii\helpers\Html::submitButton('', ['class' => 'btn-edit-open btn btn-primary glyphicon glyphicon-pencil', 'name' => 'edit-button', 'title' => 'Редактировать']) ?>
                </div>
                <?php if (isset($category['childs'])): ?>
                    <?php subcategories($category['childs'], 1, $linecategories) ?>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <hr>
    <div id="dialog" style="display:none" title="Удаление категории">Удалить категорию?</div>
</div>

<?php
include 'settings-footer.php';


function subcategories($cats, $lvl, $linecategories)
{
    foreach ($cats as $category):?>
        <div class="model-item agency-block block-lvl" style="margin-left: <?= $lvl * 30 ?>px"
             data-id="<?= $category['id'] ?>">
            <div class="agency-name">
                <div class="tab1">
                    <p class="title"><?= $category['num'] . ' ' . $category['name'] . ($category['ism'] ? ' <span title="Есть модели, привязанные к данной категории">(м)</span>' : '') ?></p>
                </div>
                <div class="tab2">
                    <label>Номер категории: <input class='input-edit-num' type="text" value="<?= $category['num'] ?>"></label>
                    <input class='input-edit-name' type="text" value="<?= $category['name'] ?>">
                    <label>Родительская категория: </label>
                    <select>
                        <?php foreach ($linecategories as $cat):?>
                            <option <?= ($cat['id_par'] == $cat['id'] ? 'selected' : '')?> value="<?= $cat['id']?>"><?= $cat['num'] . ' ' . $cat['name']?></option>
                        <?php endforeach;?>
                    </select>

                    <?= \yii\helpers\Html::submitButton('', ['class' => 'btn-save-category btn btn-success glyphicon glyphicon-ok', 'name' => 'save-button', 'title' => 'Сохранить']) ?>
                    <?= \yii\helpers\Html::submitButton('', ['class' => 'btn-edit-close btn btn-danger glyphicon glyphicon-remove', 'name' => 'close-button', 'title' => 'Отмена']) ?>
                </div>
            </div>
            <?= \yii\helpers\Html::submitButton('', ['class' => 'btn-del-category btn btn-danger glyphicon glyphicon-remove', 'name' => 'del-button', 'title' => 'Удалить категорию']) ?>
            <?= \yii\helpers\Html::submitButton('', ['class' => 'btn-edit-open btn btn-primary glyphicon glyphicon-pencil', 'name' => 'edit-button', 'title' => 'Редактировать']) ?>
        </div>
        <?php if (isset($category['childs'])): ?>
            <?php subcategories($category['childs'], $lvl + 1, $linecategories) ?>
        <?php endif; ?>
    <?php endforeach;
}

?>
