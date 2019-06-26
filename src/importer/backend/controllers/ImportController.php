<?php
namespace ant\importer\backend\controllers;

use common\modules\importer\models\ImportForm;
use trntv\filekit\actions\DeleteAction;
use trntv\filekit\actions\UploadAction;

class ImportController extends \yii\web\Controller {
    public function actions() {
        return [
            'file-upload' => [
                'class' => UploadAction::className(),
                'deleteRoute' => 'file-delete',
                'on afterSave' => function ($event) {
					
                }
            ],
            'file-delete' => [
                'class' => DeleteAction::className()
            ],
        ];
    }

    public function actionIndex($type = null) {
        $model = $this->module->getFormModel('import');
        $model->type = $type;

        if ($model->load(\Yii::$app->request->post())) {
            $dataProvider = $model->readAsDataProvider(0, 10);
            $model->process();
        }

        return $this->render($this->action->id, [
            'model' => $model,
            'dataProvider' => isset($dataProvider) ? $dataProvider : null,
        ]);
    }

    public function actionSelectColumn() {

    }

    public function actionConfirm() {

    }

    public function actionImport() {
        
    }
}