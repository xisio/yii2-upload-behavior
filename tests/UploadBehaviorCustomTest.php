<?php

namespace tests;

use tests\models\Document;
use tests\models\File;
use Yii;
use yii\base\InvalidArgumentException;
use yii\web\UploadedFile;

/**
 * Class UploadBehaviorTest
 */
class UploadBehaviorCustomTest extends DatabaseTestCase
{
    public function testUploadFileFromUrl() {

        $document = Document::findOne(3);
        $this->assertEquals('file-3.jpg', $document->file);

        $document->setScenario('update');

        $document->uploadFromUrl('file', 'https://www.google.com/robots.txt');
        $document->save();
        $this->assertEquals('robots.txt', $document->file);
    }

    public function testUploadFileFromUrlNotExistException() {

        $document = Document::findOne(3);
        $this->assertEquals('file-3.jpg', $document->file);

        $document->setScenario('update');

        $this->setExpectedException(InvalidArgumentException::class);
        $document->uploadFromUrl('file', 'https://google.com/sdfsdf');
        $document->save();

    }

    public function testUploadFileFromFile() {

        $document = Document::findOne(3);
        $this->assertEquals('file-3.jpg', $document->file);

        $document->setScenario('update');

        $document->uploadFromFile('file', __DIR__ . '/data/test-file-other.txt');
        $document->save();
        $this->assertEquals('test-file-other.txt', $document->file);
    }

    public function testUploadFileFromFileNotExistException()
    {
        $document = Document::findOne(3);
        $this->assertEquals('file-3.jpg', $document->file);
        $document->setScenario('update');

        $this->setExpectedException(InvalidArgumentException::class);

        $document->uploadFromFile('file', __DIR__ . '/data/not_exist_file.txt');
        $document->save();
    }

    public function testUploadFileFromFileWithFailValidation()
    {
        $document = Document::findOne(3);
        $this->assertEquals('file-3.jpg', $document->file);
        $document->setScenario('update');

        $document->uploadFromFile('file', __DIR__ . '/data/test-image.jpg');
        $document->save();
        $this->assertEquals('file-3.jpg', $document->file);
    }

}
