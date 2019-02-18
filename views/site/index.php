<?php

/* @var $this yii\web\View */

$this->title = 'Скачать КП';
?>
<div class="site-index">
    <div class="body-content main-page">
        <div class="main-page-section">
        <?php if(count($cats) > 0):?>
            <div class="selects-block">
                <label for="cat-select">Выберите категорию товара</label>
                <select id="cat-select" class="select-item-cat form-control">
                    <option value="Выберите категорию" data-ism="0" data-id="0" selected>Выберите категорию</option>
                    <?php foreach ($cats as $cat):
                        $name = $cat['num'] . ' ' . $cat['name']?>
                    <option value="<?= $name?>" data-ism="<?= $cat['ism']?>" data-id="<?= $cat['id']?>"><?= $name?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="btn-block">
                <button type="button" id="btn-view-mdl" class="btn btn-default" aria-label="Left Align">
                    Смотреть модели
                </button>
            </div>
        <?php else:?>
            <div class="selects-block">
                <label for="cat-select">Список категорий пуст!</label>
            </div>
        <?php endif;?>
        </div>
    </div>
</div>
