<?php
/**
 * OhUpload - PHP Uploads Made Easy!
 *
 * @author      Roger Thomas <roger.thomas@rogerethomas.com>
 * @copyright   2013 Roger Thomas
 * @link        http://www.rogerethomas.com
 * @license     http://www.rogerethomas.com/license
 * @since       0.0.1
 * @package     OhUpload
 *
 * The MIT License (MIT)
 *
 * Copyright (c) 2014 <Roger Thomas>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in
 * the Software without restriction, including without limitation the rights to
 * use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
 * the Software, and to permit persons to whom the Software is furnished to do so,
 * subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
 * FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
 * COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
 * IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
 * CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
namespace OhUploadTest;

use OhUpload\Upload;

class OhUploadTest extends \PHPUnit_Framework_TestCase
{
    public function testSettersAndGetters()
    {
        $instance = new Upload();
        $instance->setRenameFunction(
            function ($name) {
                return $name . 'foo';
            }
        );
        $this->assertInstanceOf('\Closure', $instance->getRenameFunction());
        $this->assertNull($instance->getTargetDirectory());
        $this->assertInstanceOf('\OhUpload\Upload', $instance->setTargetDirectory('/some/path'));
        $this->assertNotEmpty($instance->getValidators());
        $this->assertInstanceOf('\OhUpload\Upload', $instance->setValidators(array('\OhUpload\Validate\ErrorCode')));
        $this->assertCount(1, $instance->getValidators());
        $this->assertInstanceOf('\OhUpload\Upload', $instance->addValidator('\OhUpload\Validate\IsUploadedFile'));
        $this->assertCount(2, $instance->getValidators());
        $this->assertEquals('/some/path', $instance->getTargetDirectory());
    }

    public function testInvalidValidators()
    {
        $instance = new Upload();
        $count = count($instance->getValidators());
        $instance->addValidator('\OhUpload\Validate\IsUploadedFile');
        $this->assertCount(($count + 1), $instance->getValidators());

        $newCount = count($instance->getValidators());
        $instance->addValidator('\No\Such\Validation\Class');
        $this->assertCount($newCount, $instance->getValidators());

        $instance->addValidator('\stdClass'); // Any valid default class in PHP Core will do.
        $this->assertCount($newCount, $instance->getValidators());
    }

    public function testNonCallableRenameMethodThrowsException()
    {
        $instance = new Upload();
        try {
            $instance->setRenameFunction('foo');
        } catch (\Exception $e) {
            $this->assertInstanceOf('\OhUpload\Exception\RenameMethodMustBeCallableException', $e);
            return;
        }

        $this->fail('Expected Exception');
    }

    public function testInvalidTargetDirectoryThrowsException()
    {
        $instance = new Upload();
        try {
            $instance->receive();
        } catch (\Exception $e) {
            $this->assertInstanceOf('\OhUpload\Exception\TargetDirectoryMustBeSetAndBeWritableException', $e);
            return;
        }

        $this->fail('Expected Exception');
    }

    public function testRenameMethod()
    {
        $instance = new Upload();
        $this->assertNotContains('foo', $instance->generateFileNameFromGivenName('foobar'));
        $this->assertStringEndsWith('.jpg', $instance->generateFileNameFromGivenName('foobar.jpg'));
        $instance->setRenameFunction(
            function ($name) {
                return $name . 'test';
            }
        );
        $this->assertStringEndsWith('test', $instance->generateFileNameFromGivenName('foobar.jpg'));
    }

    public function testInvalidCodeThrowsExceptionReturn()
    {
        $_FILES = array(
            'file' => array(
                'error' => 1,
            )
        );

        try {
            $instance = new Upload();
            $instance->setTargetDirectory(sys_get_temp_dir());
            $instance->receive();
        } catch (\Exception $e) {
            $this->assertInstanceOf('\OhUpload\Validate\Exception\MaximumFileSizeExceededException', $e);
            return;
        }

        $this->fail('expected exception');
    }

    public function testReceivingFileIsValid()
    {
        $tmpFileObject = $this->getTemporaryFileName();

        $_FILES = array(
            'file' => array(
                'name' => 'foobar.txt',
                'tmp_name' => $tmpFileObject->__toString(),
                'type' => 'application/txt',
                'error' => 0,
                'size' => $tmpFileObject->getSize()
            )
        );

        $instance = new Upload();
        $instance->setTargetDirectory(sys_get_temp_dir());
        $instance->setValidators($this->getValidatorsFromInstance($instance));
        $instance->useMoveUploadedFile(false);
        $this->assertTrue($instance->receive());
        $this->assertNotNull($instance->getFinalPath());
        $this->removeTemporaryFile($tmpFileObject->__toString());
    }

    public function testReceivingFileIsInvalidWhenInvalidTargetDirectory()
    {
        $tmpFileObject = $this->getTemporaryFileName();

        $_FILES = array(
            'file' => array(
                'name' => 'foobar.txt',
                'tmp_name' => $tmpFileObject->__toString(),
                'type' => 'application/txt',
                'error' => 0,
                'size' => $tmpFileObject->getSize()
            )
        );

        $instance = new Upload();
        $instance->setTargetDirectory(sys_get_temp_dir());
        $instance->setValidators($this->getValidatorsFromInstance($instance));
        $this->assertFalse($instance->receive());
        $this->removeTemporaryFile($tmpFileObject->__toString());
    }

    public function testReceivingFileReturnsFalseAtEndOfMethod()
    {
        $instance = new Upload();
        $instance->setTargetDirectory(sys_get_temp_dir());
        $this->assertFalse($instance->receive());
    }

    /**
     * Create a temporary file and return the full path
     * @return \SplFileInfo
     */
    protected function getTemporaryFileName()
    {
        $path = sys_get_temp_dir() . '/OhUploadTest-' . uniqid();
        $handle = fopen($path, 'w');
        fwrite($handle, 'Created by ' . __CLASS__ . PHP_EOL . ' - It is safe to delete this file.');
        fclose($handle);
        return new \SplFileInfo($path);
    }

    /**
     * Attempt to delete a file
     * @param string $path
     */
    protected function removeTemporaryFile($path)
    {
        @unlink($path);
    }

    /**
     * Clean the validators, specifically removing the IsUploadedFile validator
     * @param Upload $instance
     * @return multitype:
     */
    protected function getValidatorsFromInstance(Upload $instance)
    {
        $validators = $instance->getValidators();
        foreach ($validators as $key => $validator) {
            if ($validator === '\OhUpload\Validate\IsUploadedFile') {
                unset($validators[$key]);
            }
        }
        return $validators;
    }
}
