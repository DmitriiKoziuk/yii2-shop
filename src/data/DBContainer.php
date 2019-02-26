<?php
namespace DmitriiKoziuk\yii2Shop\data;

use yii\db\Connection;

/**
 * Class DBContainer
 * @package DmitriiKoziuk\yii2Shop\data
 * Use this class for access to db from jobs.
 */
class DBContainer
{
    private $_db;

    public function __construct(Connection $db)
    {
        $this->_db = $db;
    }

    public function getDb(): Connection
    {
        return $this->_db;
    }
}