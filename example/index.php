<?php
require '../test/bootstrap.php';
?>
<html>
    <body>

        <?php
        if (!empty($_FILES)) {
            $instance = new \OhUpload\Upload('file');
            $instance->setTargetDirectory(sys_get_temp_dir());
            $instance->setRenameFunction(
                function ($name) {
                    return 'OhUpload-Example-' . $name;
                }
            );
            try {
                if ($instance->receive() === true) {
                    echo '<h2><span style="color:green;">Upload Success!</span></h2>';
                    echo '<p>Your file is located in: "' . $instance->getFinalPath() . '"</p>';
                } else {
                    echo '<h2><span style="color:red;">Upload Failed</span></h2>';
                    echo '<p>Specified field name was either not found, or the copying of the file failed.</p>';
                }
            } catch (\Exception $e) {
                echo '<h2><span style="color:red;">Exception:</span></h2>';
                echo '<p>' . get_class($e) . '</p>';
                echo '<p>' . $e->getMessage() . '</p>';
            };
            echo '<hr>';
        }
        ?>

        <h2>OhUpload Example</h2>
        <p>Select a file below, and click 'Submit'. Your file will be saved to the 'sys_get_temp_dir()' resolved directory.</p>
        <form action="upload_file.php" method="post" enctype="multipart/form-data">
            <label for="file">File:</label>
            <input type="file" name="file" id="file"><br>
            <input type="submit" name="submit" value="Submit">
        </form>

    </body>
</html>
