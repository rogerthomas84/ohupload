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
namespace OhUpload;

use OhUpload\Exception\RenameMethodMustBeCallableException;
use OhUpload\Exception\TargetDirectoryMustBeSetAndBeWritableException;

class Upload
{
    /**
     * @var string|null
     */
    protected $fieldName = null;

    /**
     * @var \Closure|null
     */
    protected $renameMethod = null;

    /**
     * @var string|null
     */
    protected $targetDirectory = null;

    /**
     * @var string|null
     */
    protected $finalPath = null;

    /**
     * @var boolean
     */
    protected $useMoveUploadedFile = true;

    /**
     * @var ValidationHandler|null
     */
    protected $validationHandler = null;

    /**
     * Construct, giving the name of the field to receive
     * @param string $fieldName
     */
    public function __construct($fieldName = 'file')
    {
        $this->validationHandler = new ValidationHandler();
        $this->fieldName = $fieldName;
        $this->renameMethod = function ($name) {
            $newName = uniqid((string) time() . '-');
            if (strstr($name, '.')) {
                $pieces = explode('.', $name);
                $newName .= '.' . $pieces[(count($pieces) - 1)];
            }

            return $newName;
        };
    }

    /**
     * Set the rename method
     * @param mixed<\Closure> $closure
     * @throws RenameMethodMustBeCallableException
     * @return \OhUpload\OhUpload
     */
    public function setRenameFunction($closure)
    {
        if (!is_callable($closure)) {
            throw new RenameMethodMustBeCallableException('Parameter given in: "' . __METHOD__ . '" must be a closure');
        }
        $this->renameMethod = $closure;

        return $this;
    }

    /**
     * Get the rename method
     * @return \Closure
     */
    public function getRenameFunction()
    {
        return $this->renameMethod;
    }

    /**
     * Get a generated name from the given callback. This will not be
     * the final name of the file, so this should only be used to establish
     * unique names (should you need it).
     * @param string $name
     * @return string
     */
    public function generateFileNameFromGivenName($name)
    {
        return call_user_func($this->renameMethod, $name);
    }

    /**
     * @return string|null
     */
    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }

    /**
     * @param string $dir
     * @return \OhUpload\OhUpload
     */
    public function setTargetDirectory($dir)
    {
        $this->targetDirectory = $dir;

        return $this;
    }

    /**
     * Whether to use move_uploaded_file or not.
     * @param boolean  $bool
     * @return \OhUpload\OhUpload
     */
    public function useMoveUploadedFile($bool)
    {
        $this->useMoveUploadedFile = $bool;

        return $this;
    }

    /**
     * Receive the file and return the status.
     * @throws TargetDirectoryMustBeSetAndBeWritableException
     * @throws \Exception (see Exceptions in \OhUpload\Validate\Exception namespace)
     * @return boolean
     */
    public function receive()
    {
        if (!is_dir($this->targetDirectory) || !is_writable($this->targetDirectory)) {
            throw new TargetDirectoryMustBeSetAndBeWritableException('Target directory of: "' . $this->targetDirectory . '" must be writable.');
        }

        if (array_key_exists($this->fieldName, $_FILES)) {
            $upload = $_FILES[$this->fieldName];
            $this->validationHandler->run($upload);
            $newName = $this->generateFileNameFromGivenName($upload['name']);

            $newPath = rtrim($this->targetDirectory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $newName;
            if ($this->useMoveUploadedFile === false) {
                $copyFunction = 'copy';
            } else {
                $copyFunction = 'move_uploaded_file';
            }

            if (call_user_func($copyFunction, $upload['tmp_name'], $newPath) === true) {
                $this->finalPath = $newPath;
                return true;
            }
        }

        return false;
    }

    /**
     * Retrieve the current array of fqcn
     * @return array
     */
    public function getValidators()
    {
        return $this->validationHandler->get();
    }

    /**
     * Retrieve the final path
     * @return string|null
     */
    public function getFinalPath()
    {
        return $this->finalPath;
    }

    /**
     * Get the final file name
     * @return string
     */
    public function getFinalFileName()
    {
        return basename($this->finalPath);
    }

    /**
     * Set the array of validators
     * @param array $validators
     * @return \OhUpload\Upload
     */
    public function setValidators(array $validators)
    {
        $this->validationHandler->set($validators);

        return $this;
    }

    /**
     * Add a single validator to the stack. This must extend
     * \OhUpload\Validate\ValidateBase
     * @param string $fqcn
     * @return \OhUpload\OhUpload
     */
    public function addValidator($fqcn)
    {
        $this->validationHandler->add($fqcn);

        return $this;
    }
}
