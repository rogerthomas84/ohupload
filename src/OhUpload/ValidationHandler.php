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

use OhUpload\Validate\ValidateBase;

class ValidationHandler
{
    /**
     * @var array
     */
    protected $validators = array(
        '\OhUpload\Validate\ErrorCode',
        '\OhUpload\Validate\IsUploadedFile'
    );

    /**
     * Retrieve the current array of fqcn
     * @return array
     */
    public function get()
    {
        return $this->validators;
    }

    /**
     * Run the stack of validators.
     * @param array $file
     * @return boolean
     */
    public function run(array $file)
    {
        foreach ($this->validators as $fqcn)
        {
            $validator = new $fqcn($file);
            /* @var $validator \OhUpload\Validate\ValidateBase */
            if (($valid = $validator->isValid()) instanceof \Exception) {
                throw $valid;
            }
        }

        return true;
    }

    /**
     * Set the array of validators
     * @param array $validators
     * @return void
     */
    public function set(array $validators)
    {
        $this->validators = array();
        foreach ($validators as $fqcn) {
            if ($this->isValidator($fqcn) === true) {
                $this->validators[] = $fqcn;
            }
        }

        return;
    }

    /**
     * Add a single validator to the stack. This must extend
     * \OhUpload\Validate\ValidateBase
     * @param string $fqcn
     * @return boolean
     */
    public function add($fqcn)
    {
        if ($this->isValidator($fqcn) === true) {
            $this->validators[] = $fqcn;
            return true;
        }

        return false;
    }

    /**
     * Check that the given class extends ValidateBase
     * @param string $fqcn
     * @return boolean
     */
    protected function isValidator($fqcn)
    {
        if (class_exists($fqcn)) {
            $instance = new $fqcn(array());
            if (!$instance instanceof ValidateBase) {
                return false;
            }

            return true;
        }

        return false;
    }
}
