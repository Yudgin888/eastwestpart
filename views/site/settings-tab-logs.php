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
                'message',
                'status',

            ],
        ]); ?>
    </div>

<?php
include 'settings-footer.php';
?>