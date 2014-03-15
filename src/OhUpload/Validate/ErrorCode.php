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
namespace OhUpload\Validate;

use OhUpload\Validate\Exception\MaximumFileSizeExceededException;
use OhUpload\Validate\Exception\UnknownInvalidErrorCodeException;
use OhUpload\Validate\Exception\NoFileUploadedException;
use OhUpload\Validate\Exception\FailedToWriteToDiskException;
use OhUpload\Validate\Exception\TmpFolderMissingException;
use OhUpload\Validate\Exception\ExtensionStoppedUploadException;

class ErrorCode extends ValidateBase
{
    /**
     * Validate the error code, and if applicable, return an Exception to be thrown.
     * @return
     *   \OhUpload\Validate\Exception\UnknownInvalidErrorCodeException
     *   \OhUpload\Validate\Exception\MaximumFileSizeExceededException
     *   \OhUpload\Validate\Exception\NoFileUploadedException
     *   \OhUpload\Validate\Exception\TmpFolderMissingException
     *   \OhUpload\Validate\Exception\FailedToWriteToDiskException
     *   \OhUpload\Validate\Exception\ExtensionStoppedUploadException
     *   boolean true
     */
    public function isValid()
    {
        $code = @$this->file['error'];

        if (!is_integer($code)) {
            return new UnknownInvalidErrorCodeException(
                'Upload error code was not an integer. Got "' . gettype($code) . '"'
            );
        }

        switch($code) {
            case(0):
                return true;
            case(1):
                return new MaximumFileSizeExceededException(
                    'The uploaded file exceeds the upload_max_filesize directive in php.ini.'
                );
                break;
            case(2):
                return new MaximumFileSizeExceededException(
                   'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.'
                );
                break;
            case(4):
                return new NoFileUploadedException(
                   'No file was uploaded.'
                );
                break;
            case(6):
                return new TmpFolderMissingException(
                   'Missing a temporary folder. Introduced in PHP 4.3.10 and PHP 5.0.3.'
                );
                break;
            case(7):
                return new FailedToWriteToDiskException(
                   'Failed to write file to disk. Introduced in PHP 5.1.0.'
                );
                break;
            case(8):
                return new ExtensionStoppedUploadException(
                   'A PHP extension stopped the file upload. PHP does not provide a way to ascertain which '
                   . 'extension caused the file upload to stop; examining the list of loaded extensions with '
                   . 'phpinfo() may help. Introduced in PHP 5.2.0.'
                );
                break;
            default:
                break;
        }

        return new UnknownInvalidErrorCodeException(
            'Unhandled error code detected. Received: "' . $code . '"'
        );
    }
}