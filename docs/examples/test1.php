<?php


require_once 'PHP/Parser.php';

// this uses the very simple parser. (Core.jay)
// you can use this as a base to write your own Parser

$p = PHP_Parser::factory();
print_r($p->parseString(file_get_contents(__FILE__)));


class test { 
    function test() {
       echo "hello world";
    }
}


