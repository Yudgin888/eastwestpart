<div class="pdf-content">
    <h2>Стоимость, условия и сроки поставки, условия оплаты</h2>
    <table>
        <thead>
        <tr>
            <th>Наименование</th>
            <th class="center">Цена с НДС,<br> доллар США</th>
        </tr>
        </thead>
        <tbody>
        <?php $issetopt = false;
            $total = 0.0;
        ?>
        <?php foreach ($options as $opt):
            $total += floatval($opt['cost']);
            if(!$issetopt && $opt['basic'] === '0'):
                $issetopt = true;?>
                <tr>
                    <td colspan="2" style="font-weight: bold;">Опции:</td>
                </tr>
            <?php endif;?>
            <tr>
                <td><?= $opt['name']?></td>
                <td class="center"><?= $opt['cost']?></td>
            </tr>
        <?php endforeach;?>
            <tr>
                <td style="font-weight: bold;">Итого:</td>
                <td class="center"><?= $total?></td>
            </tr>
        </tbody>
    </table>

    <div class="city_del">
        <?php if(!empty($city)):?>
            <p><b>Город доставки:</b> <?= $city?></p>
        <?php endif;?>
        <?php if(!empty($cost)):?>
            <p><b>Стоимость доставки:</b> $<?= $cost?></p>
            <p><b>Итого с доставкой:</b> $<?= $cost + $total?></p>
        <?php endif;?>
    </div>
</div>

<style type="text/css">
    .pdf-content {
        font-family: dompdf_times;
        width: 815px;
    }
    h2 {
        font-size: 18px;
        margin-top: 20px;
        margin-bottom: 20px;
    }
    table {
        font-size: 16px;
        width: 700px;
        border-collapse: collapse;
    }
    table td, table th {
        border: 1px solid black;
    }
    table .center {
        text-align: center;
        width: 30%;
    }
    .city_del {
        margin-top: 30px;
    }
    .city_del p {
        margin: 5px 0;
    }
</style>