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
namespace OhUploadTest\Validate;

use OhUpload\Validate\ErrorCode;

class ErrorCodeTest extends \PHPUnit_Framework_TestCase
{
    public function testAllErrorCodes()
    {
        $codes = array(
            'foo' => '\OhUpload\Validate\Exception\UnknownInvalidErrorCodeException',
            1 => '\OhUpload\Validate\Exception\MaximumFileSizeExceededException',
            2 => '\OhUpload\Validate\Exception\MaximumFileSizeExceededException',
            4 => '\OhUpload\Validate\Exception\NoFileUploadedException',
            6 => '\OhUpload\Validate\Exception\TmpFolderMissingException',
            7 => '\OhUpload\Validate\Exception\FailedToWriteToDiskException',
            8 => '\OhUpload\Validate\Exception\ExtensionStoppedUploadException',
            999 => '\OhUpload\Validate\Exception\UnknownInvalidErrorCodeException'
        );

        $validator = new ErrorCode(array('error' => 0));
        $this->assertTrue($validator->isValid());

        foreach ($codes as $code => $fqcn)
        {
            $validator = new ErrorCode(array('error' => $code));
            $this->assertInstanceOf($fqcn, $validator->isValid());
        }
    }
}
