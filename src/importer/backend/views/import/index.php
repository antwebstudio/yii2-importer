<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use ant\file\widgets\Upload;
use ant\widgets\Alert;
?>

<?php if (!\Yii::$app->request->post()): ?>
    <?php $form = ActiveForm::begin() ?>
        <?= $form->field($model, 'step')->hiddenInput(['value' => 'read'])->label(false) ?>

        <?= $form->field($model, 'file')->widget(
            Upload::classname(),
            [
                'url' => ['file-upload'],
            ]
        ) ?>

        <?= Html::submitButton('Upload', ['class' => 'btn btn-primary']) ?>
    <?php ActiveForm::end() ?>
<?php elseif ($model->step == 'read'): ?>
    <?php $form = ActiveForm::begin() ?>
        <?= $form->field($model, 'step')->hiddenInput(['value' => 'confirm'])->label(false) ?>

        <div style="display: none;">
            <?= $form->field($model, 'file')->widget(
                Upload::classname(),
                [
                    'url' => ['file-upload'],
                ]
            ) ?>
        </div>
        <?php
            $columnCount = count(current($model->readAsDataProvider()->getModels()));
            
            for ($i = 0; $i < $columnCount; $i++ ) {
                $widget = $form->field($model, 'importTo['.$i.']')->dropDownlist($model->getImportToDropdown(), [
                    'prompt' => '',
                ]);
                $columns[] = [
                    'header' => $model->getImportedDataHeader($i).'<br/>'.$widget,
                    'attribute' => $i,
					'headerOptions' => ['style' => 'min-width: 150px'],
                ];
            }
        ?>
        <?= \yii\grid\GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => $columns,
			'options' => ['style' => 'overflow-x: auto'],
        ]) ?>

        <?= Html::submitButton('Import', ['class' => 'btn btn-primary']) ?>
    <?php ActiveForm::end() ?>
<?php elseif ($model->step == 'confirm'): ?>
    <?php if ($model->lastModel->hasErrors()): ?>
        <?= Alert::widget() ?>
    <?php else: ?>
        <?= Alert::widget() ?>
    <?php endif ?>
    <div>Total data count: <?= $model->dataProvider->totalCount ?></div>
    <div>Imported count: <?= $model->importedCount ?></div>
    <?= Html::a('OK', ['/importer/backend/import', 'type' => $model->type], ['class' => 'btn btn-primary']) ?>
<?php endif ?>