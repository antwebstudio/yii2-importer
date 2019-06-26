<?php
namespace ant\importer\components;

class ARImportStrategy extends \ruskid\csvimporter\ARImportStrategy {
    public $lastModel;

    public function import(&$data) {
        $importedPks = [];
        foreach ($data as $row) {
            $skipImport = isset($this->skipImport) ? call_user_func($this->skipImport, $row) : false;
            if (!$skipImport) {
                /* @var $model \yii\db\ActiveRecord */
                $this->lastModel = \Yii::createObject($this->className);
                $uniqueAttributes = [];
                foreach ($this->configs as $config) {
                    if (isset($config['attribute']) && trim($config['attribute']) != '') {
                        $value = call_user_func($config['value'], $row);

                        //Create array of unique attributes
                        if (isset($config['unique']) && $config['unique']) {
                            $uniqueAttributes[$config['attribute']] = $value;
                        }

                        //Set value to the model
                        $this->lastModel->{$config['attribute']} =  $value;
                    }
                }
                //Check if model is unique and saved with success
                if (!$this->lastModel->validate()) throw new \Exception(print_r($this->lastModel->errors, 1));

                if ($this->isActiveRecordUnique($uniqueAttributes) && $this->lastModel->save()) {
                    $importedPks[] = $this->lastModel->primaryKey;
                } else {
                    throw new \Exception('Failed import');
                }
            }
        }
        return $importedPks;
    }
    
    private function isActiveRecordUnique($attributes) {
        /* @var $class \yii\db\ActiveRecord */
        $class = $this->className;
        return empty($attributes) ? true :
                !$class::find()->where($attributes)->exists();
    }
}