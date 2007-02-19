--TEST--
PHP_Parser: test unticked_function_statement 2
--FILE--
<?php
require_once 'PHP/Parser/Core.php';
require_once 'PHP/Parser/Tokenizer.php';
$a = new PHP_Parser_Tokenizer(file_get_contents(dirname(__FILE__) . '/files/unticked_function_statement2.inc'));
$b = new PHP_Parser_Core($a);
while ($a->advance()) {
    $b->doParse($a->token, $a->getValue(), $a);
}
$b->doParse(0, 0);
var_dump($b->data);
?>
===DONE===
--EXPECT--
array(2) {
  [0]=>
  array(5) {
    ["type"]=>
    string(8) "function"
    ["returnsref"]=>
    bool(false)
    ["name"]=>
    string(4) "test"
    ["parameters"]=>
    array(0) {
    }
    ["info"]=>
    array(0) {
    }
  }
  [1]=>
  array(5) {
    ["type"]=>
    string(8) "function"
    ["returnsref"]=>
    bool(false)
    ["name"]=>
    string(4) "test"
    ["parameters"]=>
    array(0) {
    }
    ["info"]=>
    array(23) {
      [0]=>
      array(1) {
        ["global"]=>
        string(2) "$a"
      }
      [1]=>
      array(1) {
        ["global"]=>
        string(2) "$a"
      }
      [2]=>
      array(2) {
        ["static"]=>
        string(2) "$a"
        ["default"]=>
        NULL
      }
      [3]=>
      array(2) {
        ["static"]=>
        string(2) "$a"
        ["default"]=>
        string(1) "1"
      }
      [4]=>
      array(2) {
        ["static"]=>
        string(2) "$a"
        ["default"]=>
        NULL
      }
      [5]=>
      array(2) {
        ["static"]=>
        string(2) "$b"
        ["default"]=>
        string(1) "1"
      }
      [6]=>
      array(2) {
        ["static"]=>
        string(2) "$a"
        ["default"]=>
        NULL
      }
      [7]=>
      array(2) {
        ["static"]=>
        string(2) "$b"
        ["default"]=>
        NULL
      }
      [8]=>
      array(1) {
        ["uses"]=>
        string(4) "'hi'"
      }
      [9]=>
      array(1) {
        ["uses"]=>
        string(6) "('hi')"
      }
      [10]=>
      array(2) {
        ["declare"]=>
        string(2) "hi"
        ["default"]=>
        string(2) " 1"
      }
      [11]=>
      array(2) {
        ["declare"]=>
        string(2) "hi"
        ["default"]=>
        string(2) " 1"
      }
      [12]=>
      array(2) {
        ["declare"]=>
        string(3) "hit"
        ["default"]=>
        string(2) " 2"
      }
      [13]=>
      array(2) {
        ["declare"]=>
        string(4) " bye"
        ["default"]=>
        string(2) " 3"
      }
      [14]=>
      array(2) {
        ["declare"]=>
        string(3) "hit"
        ["default"]=>
        string(2) " 2"
      }
      [15]=>
      array(2) {
        ["declare"]=>
        string(4) " bye"
        ["default"]=>
        string(2) " 3"
      }
      [16]=>
      array(1) {
        ["catches"]=>
        string(4) "Blah"
      }
      [17]=>
      array(1) {
        ["catches"]=>
        string(4) "Blah"
      }
      [18]=>
      array(1) {
        ["catches"]=>
        string(3) "Foo"
      }
      [19]=>
      array(1) {
        ["throws"]=>
        string(10) " Classname"
      }
      [20]=>
      array(5) {
        ["type"]=>
        string(8) "function"
        ["returnsref"]=>
        bool(false)
        ["name"]=>
        string(7) " inside"
        ["parameters"]=>
        array(0) {
        }
        ["info"]=>
        array(0) {
        }
      }
      [21]=>
      array(6) {
        ["type"]=>
        string(5) "class"
        ["modifiers"]=>
        array(0) {
        }
        ["name"]=>
        string(7) " inside"
        ["extends"]=>
        array(0) {
        }
        ["implements"]=>
        array(0) {
        }
        ["info"]=>
        array(0) {
        }
      }
      [22]=>
      array(4) {
        ["type"]=>
        string(9) "interface"
        ["name"]=>
        string(7) " inside"
        ["extends"]=>
        array(0) {
        }
        ["info"]=>
        array(0) {
        }
      }
    }
  }
}
===DONE===