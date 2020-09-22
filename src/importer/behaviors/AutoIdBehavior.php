<?php
namespace ant\importer\behaviors;

use Yii;

/**
 * Auto ID for database table
 */
class AutoIdBehavior extends \yii\base\Behavior {
    public $db;
    public $tableName;
    
    protected $autoId;
    protected $database;

    public function recordAutoId() {
        $sql = 'SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = "'.$this->getDatabaseName().'" AND TABLE_NAME = "'.$this->getTableName().'"';

        $this->autoId = $this->getDb()->createCommand($sql)->queryScalar();
    }

    public function restoreAutoId() {
        $this->getDb()->createCommand('LOCK TABLES '.$this->getTableName().' WRITE')->execute();
        $this->getDb()->createCommand('ALTER TABLE '.$this->getTableName().' AUTO_INCREMENT = '.$this->autoId)->execute();
        $this->getDb()->createCommand('UNLOCK TABLES')->execute();
    }

    protected function getTableName() {
        return $this->getDb()->getSchema()->getRawTableName($this->tableName);
    }

    protected function getDatabaseName() {
        if (!isset($this->database)) {
            $this->database = $this->getDb()->createCommand("SELECT DATABASE()")->queryScalar();
        }
        return $this->database;
    }
    
    protected function getDb() {
        if (is_callable($this->db)) {
            return call_user_func_array($this->db, []);
        }
        return $this->db;
    }
}
