<?php
    function doJob($filepath = null)
    {
        // folder where all generated the files will be
        $parentDir = 'UnitTestsAutoGen';
        // get file contents of specifiled folder or use the default folders (models & controllers)
        $dirsToRead = readDirs($filepath);
        // make the directory if it doesn't exist
        if (!is_dir($parentDir) && !file_exists($parentDir)){
            mkdir($parentDir,0755);
        }
        // loop through all the files found in the directories
        foreach ($dirsToRead as $key => $dir){
            // for each file 
            foreach ($dir as $inFile){
                // make the sub folders $parentFolder + file path 
                if (!is_dir($parentDir . $key) && !file_exists($parentDir . $key)){
                    mkdir($parentDir . $key,0755);
                }
                // file name for output
                $outFile = substr($inFile, 0, -4) . "Test.php";
                // open file to read from
                $readFile = fopen('./' . $key . '/' . $inFile, 'r');
                // open file to write to
                $writeFile = fopen($outFile, 'a+');
                // comment field before class delcaration
                $classPrepend = "/**\n *\n */\n";
                // comment field before funtion declarations
                $functionPrepend = "\t/**\n\t *\n\t */\n";
                // reset the array that will be written
                $write = array(); 
                // do work until end of file
                while(! feof($readFile)){
                    //each line
                    $line = fgets($readFile);
                    // does the line start with "class"?
                    if (preg_match('/^class /', $line)){
                        // trim and remove brackets if it exists
                        $line = trim(str_replace("{", "", $line));
                        // array of words in class declaration
                        $words = explode(' ', $line);
                        /**
                         *  text to write:
                         *  conatins php open tag, 
                         *  comment field, 
                         *  first two words in array, and 'extend UnitTest'  
                         */ 
                        $write[] = "<?php\n" . $classPrepend . $words[0] . ' ' . $words[1] . "Test extends TestCase\n{\n";
                    }
                    // do the lines contain function declarations? 
                    if (preg_match('/\s+public\sfunction/', $line)){
                        // replace any brackets on the line
                        $line = str_replace("{", "", $line);
                        // replace "public funciton" then trim
                        $line = trim(str_replace("public function", "", $line));
                        // make leftover first letter capital
                        $line = ucfirst($line);
                        // put back "public function test"
                        // and brackets with newlines & tabs for formatting
                        $line = "\tpublic function test$line\n\t{\n\n\t}\n\n";
                        // add to write array
                        $write[] = $functionPrepend . $line;
                    } else if (preg_match('/\s+private\sfunction/', $line)){
                        $line = str_replace("{", "", $line);
                        $line = trim(str_replace("private function", "", $line));
                        $line = ucfirst($line);
                        $line = "\tprivate function test$line\n\t{\n\n\t}\n\n";
                        $write[] = $functionPrepend . $line;
                    } else if (preg_match('/\s+protected\sfunction/', $line)){
                        $line = str_replace("{", "", $line);
                        $line = trim(str_replace("protected function", "", $line));
                        $line = ucfirst($line);
                        $line = "\tprotected function test$line\n\t{\n\n\t}\n\n";
                        $write[] = $functionPrepend . $line;
                    } else if (preg_match('/\s+public\sstatic\sfunction/', $line)){
                        $line = str_replace("{", "", $line);
                        $line = trim(str_replace("public static function", "", $line));
                        $line = ucfirst($line);
                        $line = "\tpublic static function test$line\n\t{\n\n\t}\n\n";
                        $write[] = $functionPrepend . $line;
                    } else if (preg_match('/\s+private\sstatic\sfunction/', $line)){
                        $line = str_replace("{", "", $line);
                        $line = trim(str_replace("private static function", "", $line));
                        $line = ucfirst($line);
                        $line = "\tprivate static function test$line\n\t{\n\n\t}\n\n";
                        $write[] = $functionPrepend . $line;
                    } else if (preg_match('/\s+protected\sstatic\sfunction/', $line)){
                        $line = str_replace("{", "", $line);
                        $line = trim(str_replace("protected static function", "", $line));
                        $line = ucfirst($line);
                        $line = "\tprotected static function test$line\n\t{\n\n\t}\n\n";
                        $write[] = $functionPrepend . $line;
                    }
                    /**
                     *  put the end of class bracket, 
                     *  check if we commented, "} // end" or  "}// end" or "}//end"
                     *  or if the line starts with a brakcet
                     */
                    if (preg_match('/\}\s\/\/\send/', $line) ||
                        preg_match('/\}\/\/\send/', $line) ||
                        preg_match('/\}\/\/end/', $line) ||
                        preg_match('/^\}/', $line)){
                        $write[] = "}\n?>";
                    }
                }
                // write to the file
                foreach ($write as $line){
                    fwrite($writeFile, $line);
                }
                // close files
                fclose($readFile);
                fclose($writeFile);
                // move created file to its proper sub folder
                rename($outFile, $parentDir . $key . '/' . $outFile);
            }
        }
    }
    function readsDirs($filepath = null)
    {
        // was a file path provided?
        if ($filepath === null){
            // default file paths
            $directories = array(
                './models',
                './controllers'
            );
        } else {
            // specified file path
            $directories = explode(',', $filepath);
        }
        // array of all folders 
        $results = array();
        foreach ($directories as $dir){
            if (is_dir($dir)){
                if ($handle = opendir($dir)){
                    $dir = str_replace('.', '', $dir);
                    while(($file = readdir($handle)) !== FALSE){
                        if ($file === '.' || $file === '..'){
                            continue;
                        } else {
                            $results[$dir][] = $file;
                            //$results[] = $file;
                        }
                    }
                    closedir($handle);
                }
            }
        }
        return $results;
    }

    function readDirs($filePath=null) {
         // was a file path provided?
         if ($filePath === null){
            // default file paths in codeigniter 3.0
            $dirs = array(
                './models',
                './controllers'
            );
        } else{
            // file path was provided it can be a comma separated list or singular filepath
            $dirs = explode(',', $filePath);
        }
        // final array
        $contents = array();
        // for each file path
        foreach ($dirs as $dir) {
            // for each possible file 
            foreach (scandir($dir) as $node) {
                if ($node == '.' || $node == '..') {
                    continue;
                }
                // if its not a directory
                if (!is_dir($dir . '/' . $node)) {
                    // does the file end in '.php'?
                    if (substr($node, -4) === '.php') {
                        // remove the leading period of directory and store file name
                        $contents[str_replace('.', '', $dir)][] = $node;
                    }
                } else {
                    // it is a directory rerun the function 
                    $recurseArray = readDirs($dir . '/' . $node);
                    $contents = array_merge($contents, $recurseArray);
                }
            }
        }
        return $contents;
    }
?>
