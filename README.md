OhUpload PHP Library
====================

## PHP Uploads Made Easy!

### Basic Usage

You can run a working example of this file by looking in the `example` folder.

```php
<?php
$instance = new OhUpload('form_file_name');
$instance->setTargetDirectory('/path/to/upload/directory');
try {
    if ($instance->receive() === true) {
        // OK.
        $finalPath = $instance->getFinalPath();
    } else {
        // Not OK!
    }
} catch (\Exception $e) {
    // Something happened, possibly a validator. Take a look in:
    // \OhUpload\Validate\Exception for possible Exceptions.
}
```


### Adding a custom validator

You can easily add a validator to the validation stack. Below is an example of a custom validator.
All custom validators must extend `\OhUpload\Validate\ValidateBase` and override the `isValid()` method. The expected return
from the `isValid()` method has to be either:
 * `\Exception` - don't throw, just return.
 * `bool(true)`

```php
<?php
namespace My\Custom\Validators;

use OhUpload\Validate\ValidateBase
use My\Custom\Validators\Exception\ExampleException;

class MyBasicCheck extends ValidateBase
{
    /**
     * Run a custom validator.
     * @return
     *   \My\Custom\Validators\Exception\ExampleException
     *   boolean true
     */
    public function isValid()
    {
        if (!empty($this->file)) { // The file from the $_FILES array
            return true;
        }

        return new ExampleException('Validation failed');
    }
}
```

To add it into the process, simply use:

```php
<?php
$instance = new OhUpload('form_file_name');
$instance->setTargetDirectory('/path/to/upload/directory');
$instance->addValidator('/My/Custom/Validators/MyBasicCheck'); // This is a string, not an instance.
try {
    if ($instance->receive() === true) {
        // OK.
        $finalPath = $instance->getFinalPath();
    } else {
        // Not OK!
    }
} catch (\My\Custom\Validators\MyBasicCheck $e) {
    // Wow! You're custom validator failed!
}
```
