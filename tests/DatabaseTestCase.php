<?php

namespace yii\web;

/**
 * Mock for the is_uploaded_file() function for web classes.
 * @return boolean
 */
function is_uploaded_file($filename)
{
    return file_exists($filename);
}

/**
 * Mock for the move_uploaded_file() function for web classes.
 * @return boolean
 */
function move_uploaded_file($filename, $destination)
{
    return copy($filename, $destination);
}

namespace tests;

use Yii;
use yii\db\Connection;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;

/**
 * DatabaseTestCase
 */
abstract class DatabaseTestCase extends \PHPUnit_Extensions_Database_TestCase
{
    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->clearDB();
        if (Yii::$app->get('db', false) === null) {
            $this->markTestSkipped();
        } else {
            FileHelper::createDirectory(__DIR__ . '/upload');
            parent::setUp();
        }
    }

    /**
     * @inheritdoc
     */
    protected function tearDown()
    {
        parent::tearDown();
        FileHelper::removeDirectory(__DIR__ . '/upload');
    }

    /**
     * @inheritdoc
     */
    public function getConnection()
    {
        return $this->createDefaultDBConnection(\Yii::$app->getDb()->pdo);
    }

    /**
     * @inheritdoc
     */
    public function getDataSet()
    {
        return $this->createFlatXMLDataSet(__DIR__ . '/data/test.xml');
    }

    protected function clearDB()
    {
        try {
            Yii::$app->set('db', [
                'class' => Connection::class,
                'dsn' => 'sqlite::memory:',
            ]);

            Yii::$app->getDb()->open();
            $lines = explode(';', file_get_contents(__DIR__ . '/migrations/sqlite.sql'));

            foreach ($lines as $line) {
                if (trim($line) !== '') {
                    Yii::$app->getDb()->pdo->exec($line);
                }
            }
        } catch (\Exception $e) {
            Yii::$app->clear('db');

            throw $e;
        }

        UploadedFile::reset();
    }

    public static function setUpBeforeClass()
    {
        FileHelper::createDirectory(__DIR__ . '/assets');
        parent::setUpBeforeClass();
    }

    public static function tearDownAfterClass()
    {
        FileHelper::removeDirectory(__DIR__ . '/assets');
        parent::tearDownAfterClass();
    }
}
