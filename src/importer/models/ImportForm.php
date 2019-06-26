<?php
namespace ant\importer\models;

use Yii;
use trntv\filekit\behaviors\UploadBehavior;
use ruskid\csvimporter\CSVReader;
use ruskid\csvimporter\CSVImporter;

class ImportForm extends \yii\base\Model {
    public $file;
    public $importTo;

    public $filePath;
    public $fileBaseUrl;

    public $configs = [];
    public $models = [];
    public $step;
    public $type = 'default';

    public $importStrategy = 'common\modules\importer\components\ARImportStrategy';
	
	public $timeout = 300; // seconds

    protected $_data;
    protected $_dataProvider;
    protected $_header;
    protected $_imported;
    protected $_lastModel;
    
    public function behaviors() {
        return [
            'file' => [
                'class' => UploadBehavior::className(),
                'attribute' => 'file',
                'pathAttribute' => 'filePath',
                'baseUrlAttribute' => 'fileBaseUrl'
            ],
        ];
    }

    public function rules() {
        return [
            [['file'], 'required'],
            [['file', 'importTo', 'step'], 'safe'],
        ];
    }

    public function getUploadedFilePath() {
        return $this->file['path'];
    }

    // limitLine included the header
    public function read($startFromLine = 0, $limitLine = null, $firstLineIsHeader = true) {
        if (!isset($this->_data)) {        
            $filename = $this->getBasePath() . '/'.$this->getUploadedFilePath();

            $reader = $this->getReader($filename, $startFromLine);
            $this->_data = $reader->readFile();
            if (isset($limitLine)) {
                $this->_data = array_slice($this->_data, 0, $limitLine);
            }
            if ($firstLineIsHeader) {
                $this->_header = array_shift($this->_data);
            }
        }
        return $this->_data;
    }

    public function readAsDataProvider($startFromLine = 0, $limitLine = null, $firstLineIsHeader = true) {
        $this->_dataProvider = new \yii\data\ArrayDataProvider([
            'allModels' => $this->read($startFromLine, $limitLine, $firstLineIsHeader),
        ]);
        return $this->_dataProvider;
    }

    public function getDataProvider() {
        if (isset($this->_dataProvider)) {
            $this->_dataProvider = $this->readAsDataProvider();
        }
        return $this->_dataProvider;
    }

    public function getImportedDataHeader($index = null) {
        return isset($index) ? $this->_header[$index] : $this->_header;
    }

    public function process() {
        if ($this->step == 'confirm') {
            $filename = $this->getBasePath() . '/'.$this->getUploadedFilePath();

            $transaction = \Yii::$app->db->beginTransaction();

            $strategyConfig = $this->processConfig($this->type);
            $strategy = Yii::createObject($strategyConfig['class'], [$strategyConfig]);

            try {
                $this->_imported = [];

                $importer = new CSVImporter;
                $importer->setData($this->getReader($filename, 1));

                set_time_limit($this->timeout);
                $this->_imported = $importer->import($strategy);
                $this->_lastModel = $strategy->lastModel;

                $transaction->commit();
                return $this->_imported;
            } catch (\Exception $ex) {
                $this->_lastModel = $strategy->lastModel;
                
                $transaction->rollback();

                throw $ex;
            }
        }
    }

    public function getLastModel() {
        if (isset($this->_lastModel)) {
            return $this->_lastModel;
        }
    }

    public function getImportToDropdown() {
        $dropdown = [];
        
        foreach ($this->configs[$this->type] as $configName => $config) {
            if (is_int($configName) && is_string($config)) {
                $configName = $config;
            }

            if (strpos($configName, '.') !== false) {
                list($alias, $attribute) = explode('.', $configName);
                $className = $this->models[$alias];
            } else if (count($this->models) > 1) {
                throw new \Exception('Ambigous config: "'.$configName.'" model class name. ');
            }
            $dropdown[$configName] = $this->getImportOptionLabel($alias, $attribute);
        }
        return $dropdown;
    }

    public function getImportedCount() {
        return count($this->_imported);
    }

    protected function getClassName($alias) {
        return is_array($this->models[$alias]) ? $this->models[$alias]['class'] : $this->models[$alias];
    }

    protected function getImportOptionLabel($alias, $attribute) {
        $className = $this->getClassName($alias);;
        $shortClassName = \yii\helpers\StringHelper::basename($className);
        $model = new $className;  
        return $shortClassName.' '.$model->getAttributeLabel($attribute);
    }

    protected function processConfig($type) {
        $className = $this->models[$type];
        $configName = $className;

        $importerConfigs = [];
        foreach ($this->importTo as $i => $importTo) {
            if (isset($importTo) && trim($importTo) != '') {

                // If importTo = alias.attribute
                if (strpos($importTo, '.') !== false) {
                    list($alias, $attribute) = explode('.', $importTo);
                } else if (count($this->models) > 1) {
                    throw new \Exception('Ambigous config: "'.$configName.'" model class name. ');
                }

                $config = $this->configs[$type];

                if (isset($config[$importTo]) && is_callable($config[$importTo])) {
                    $callback = $config[$importTo];
                    $valueCallback = function($line) use ($callback, $i) {
                        return call_user_func_array($callback, [trim($line[$i])]);
                    };
                } else if (isset($config[$importTo]['value']) && is_callable($config[$importTo]['value'])) {
                    $callback = $config[$importTo]['value'];
                    $valueCallback = function($line) use ($callback, $i) {
                        return call_user_func_array($callback, [trim($line[$i])]);
                    };
                } else {
                    $valueCallback = function($line) use ($i) {
                        return $line[$i];
                    };
                }

                $importerConfigs[] = [
                    //'unique' => false,
                    'attribute' => $attribute,
                    'value' => $valueCallback,
                ];
            }
        }
        return [
            'class' => $this->importStrategy,
            'className' => $className,
            'configs' => $importerConfigs,
        ];
    }

    protected function getReader($filename, $startFromLine = 1) {
        return new CSVReader([
            'filename' => $filename,
            'startFromLine' => $startFromLine,
            'fgetcsvOptions' => [
                
            ]
        ]);
    }

    protected function getBasePath() {
        return Yii::getAlias('@storage/web/source');
    }
}