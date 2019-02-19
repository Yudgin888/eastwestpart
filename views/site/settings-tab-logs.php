<?php
include 'settings-header.php';
?>
    <div class="setting-tab-item upload-cost">
        <h2>Логи:</h2>
        <?= \yii\grid\GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                'id',
                'date:ntext',
                [
                    'attribute' => 'message',
                    'value' => function ($data) {
                        $arr = explode(':', $data->message);
                        if(count($arr) > 1){
                            return \yii\helpers\Html::a(\yii\helpers\Html::encode($data->message), \yii\helpers\Url::to(array_pop($arr)));
                        } else {
                            return $data->message;
                        }
                    },
                    'format' => 'raw',
                ],
            ],
        ]); ?>
        <?= \yii\helpers\Html::submitButton('Очистить логи', ['class' => 'btn btn-primary clear-logs-button', 'name' => 'clear-button']) ?>
    </div>
    <div id="dialog" style="display:none" title="Удаление логов"></div>

<?php
include 'settings-footer.php';
?>