<div class="pdf-content">
    <?php
        $basic = array_filter($options, function($item){
            return intval($item['basic']);
        });
        $opt = array_filter($options, function($item){
            return !intval($item['basic']);
        });
    ?>
    <div class='options_content'>
        <?php if(count($basic) > 0):?>
            <div class='block'>
                <p>Базовая комплектация:</p>
                <ul>
                    <?php foreach ($basic as $item):?>
                    <li><?= $item['name']?></li>
                    <?php endforeach;?>
                </ul>
            </div>
        <?php endif;?>
        <?php if(count($opt) > 0):?>
            <div class='block'>
                <p>Опции:</p>
                <ul>
                    <?php foreach ($opt as $item):?>
                        <li><?= $item['name']?></li>
                    <?php endforeach;?>
                </ul>
            </div>
        <?php endif;?>
    </div>
    <div class="city_del">
        <?php if(!empty($city)):?>
            <p><b>Город доставки:</b> <?= $city?></p>
        <?php endif;?>
        <?php if(!empty($cost)):?>
            <p><b>Стоимость доставки:</b> $<?= $cost?></p>
        <?php endif;?>
    </div>
</div>

<style type="text/css">
    .pdf-content {
        font-family: dompdf_times;
        width: 815px;
    }
    .pdf-content .options_content {
        margin-top: 40px;
        font-size: 16px;
    }
    .block {
        display: inline-block;
        width: 45%;
        margin: 5px 10px;
        vertical-align: top;
    }
    .block p {
        margin: 0;
    }
    .city_del {
        margin-top: 30px;
    }
    .city_del p {
        margin: 5px 0;
    }
</style>