
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
                <?php $issetopt = false;?>
                <?php foreach ($options as $opt):
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
                </tbody>
            </table>

        </div>

        <style type="text/css">
            h2 {
                font-size: 18px;
            }
            .pdf-content {
                font-family: times-roman;
                width: 815px;
            }
            table {
                font-size: 16px;
                width: 700px;
                border-collapse: collapse;
            }
            table td, table th {
                padding-bottom: 8px;
                padding-right: 10px;
                border: 1px solid black;
            }
            table .center {
                text-align: center;
                width: 30%;
            }
        </style>
