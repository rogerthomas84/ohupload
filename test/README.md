# Test Notes

The tests assume that the `$_FILES` array looks like:

```php
<?php
array(1) {
  'file' =>
  array(5) {
    'name' =>
    string(42) "originalfile.zip"
    'type' =>
    string(15) "application/zip"
    'tmp_name' =>
    string(14) "/tmp/phpcjfg6Y"
    'error' =>
    int(0)
    'size' =>
    int(12345)
  }
}
```

To emulate a successful upload, you'll need to use the `\OhUpload\OhUpload::useMoveUploadedFile($bool)` method to tell
OhUpload to use the `copy()` method versus the `move_uploaded_file()` method.

