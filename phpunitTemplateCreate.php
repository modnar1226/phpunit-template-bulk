<?php
    function doJob($filepath = null)
    {
        // folder where all generated the files will be
        $parentDir = 'UnitTestsAutoGen';
        // get file contents of specifiled folder or use the default folders (models & controllers)
        $dirsToRead = getPathContents($filepath);
        // make the directory if it doesn't exist
        if (!is_dir($parentDir) && !file_exists($parentDir)){
            mkdir($parentDir,0755);
        }
        // loop through all the files found in the directories
        foreach ($dirsToRead as $key => $dir){
            // for each file 
            foreach ($dir as $inFile){
                
                // file name for output
                $outFile = substr($inFile, 0, -4) . "Test.php";
                // open file to read from
                if ($key === '.') {
                    $readFile = fopen($key . '/' . $inFile, 'r');
                } else {
                    $readFile = fopen('./' . $key . '/' . $inFile, 'r');
                }
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
                // close read file
                fclose($readFile);

                // check that there is something to write
                if (!empty($write)) {
                    $allowedToWrite = false;
                    foreach ($write as $line) {
                        if (preg_match('/class/', $line) || preg_match('/function/', $line)) {
                            $allowedToWrite = true;
                            break;
                        }
                    }
                    // make the sub folders $parentFolder + file path 
                    if ($allowedToWrite) {
                        if ($key !== '.') {
                            if (!is_dir($parentDir . $key) && !file_exists($parentDir . $key)){
                                //echo $key . "\n";
                                mkdir($parentDir . $key, 0755, true);
                            }
                        }
                        // open file to write to
                        $writeFile = fopen($outFile, 'a+');
                        // write to the file
                        foreach ($write as $line){
                            fwrite($writeFile, $line);
                        }
                        // close write file
                        fclose($writeFile);
                        // move created file to its proper sub folder
                        if ($key !== '.') {
                            rename($outFile, $parentDir . $key . '/' . $outFile);
                        } else {
                            rename($outFile, $parentDir . '/' . $outFile);
                        }
                    }
                }
            }
        }
    }
    
    function getPathContents($path)
    {
         // was a file path provided?
         if ($path === null){
            // default file paths in codeigniter 3.0
            $dirs = array(
                './models',
                './controllers'
            );
        } else{
            // file path was provided
            // it can be a comma separated list or singular filepath or file
            $dirs = explode(',', $path);
        }
        // final array
        $contents = array();
        // for each directory / file given
        foreach ($dirs as $dir) {
            // is it a directory?
            if (is_dir($dir)) {
                // scan the directory and for each file inside
                foreach (scandir($dir) as $node) {
                    // skip current and parent directory
                    if ($node === '.' || $node === '..') {
                        continue;
                    } else {
                        // check for sub directories
                        if (is_dir($dir . '/' . $node)) {
                            // recursive check for sub directories
                            $recurseArray = getPathContents($dir . '/' . $node);
                            // merge current and recursive results
                            $contents = array_merge($contents, $recurseArray);
                        } else {
                            // it a file, put it in the array if it's extension is '.php'
                            if (substr($node, -4) === '.php') {
                                // don'r remove periods if current or parent directory was input
                                if ($dir === '.' || $dir === '..') {
                                    $contents[$dir][] = $node;
                                } else {
                                    // remove period from directory name 
                                    $contents[str_replace('.', '', $dir)][] = $node;
                                }
                            }
                        }
                    }
                }
            } else {
                // file name was given
                $contents[] = $dir; 
            }
        }
        return $contents;
    }
?>
