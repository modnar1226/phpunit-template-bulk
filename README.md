# phpunit-template-bulk
Create templates for phpunit testing | recursive | linux | codeigniter 3.0 |

STEP 1: 

doJob('your/file/path/containing/php/files/');

STEP 2:

Open folder UnitTestsAutoGen/ 

Inside will be a clone of the directory structure with template files cloned from existing classes and functions. Only files that contain classes and or functions will be cloned.

The files are named and written to be ready to test via phpunit. The only thing they need is for you to write the tests for your code inside the templates.

Example file being read:

```
<?php

class Hello extends CI_Controller{
  public function index()
  {
   	#code ...
  }

  private function test1()
  {
  	#code ...
  }

  protected function test2()
  {
  	#code ...
  } 

  private static function test3($param1, $param2)
  {
  	#code ...
  }

  public static function test4($param1, $param2)
  {
  	#code ...
  }

  protected static function test5($param1, $param2)
  {
  	#code ...
  }
}

```

Example output file:

```
<?php
/**
 *
 */
class HelloTest extends TestCase
{
  /**
   *
   */
  public function testIndex()
  {

  }

  /**
   *
   */
  private function testTest1()
  {

  }

  /**
   *
   */
  protected function testTest2()
  {

  }

  /**
   *
   */
  private static function testTest3($param1, $param2)
  {

  }

  /**
   *
   */
  public static function testTest4($param1, $param2)
  {

  }

  /**
   *
   */
  protected static function testTest5($param1, $param2)
  {

  }

}
?>
```
