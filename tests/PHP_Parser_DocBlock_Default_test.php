<?php

/**
 * API Unit tests for PHP_Parser package.
 * 
 * @version    $Id$
 * @author     Laurent Laville <pear@laurent-laville.org> portions from HTML_CSS
 * @author     Greg Beaver
 * @package    PHP_Parser
 */

/**
 * @package PHP_Parser
 */

class PHP_Parser_DocBlock_Default_test extends PHPUnit_TestCase
{
    /**
     * A PHP_Parser_DocBlock_DefaultLexer object (or one of the drivers)
     * @var        object
     */
    var $lexer;
    /**
     * This will be Error_Stack if the package is accepted into PEAR
     */
    var $errorStackClass = 'PEAR_ErrorStack';
    
    /**#@+
     * Error Stacks
     */
    var $pstack;
    var $sstack;
    var $estack;

    function PHP_Parser_DocBlock_DefaultLexer_tag_test($name)
    {
        $this->PHPUnit_TestCase($name);
    }

    function setUp()
    {
        error_reporting(E_ALL);
        $this->errorOccured = false;
        set_error_handler(array(&$this, 'errorHandler'));

        $this->parser = new PHP_Parser_DocBlock_Default;
        $this->_expectedErrors = array();
        $this->_testMethod = 'unknown';
    }

    function tearDown()
    {
        unset($this->parser);
        restore_error_handler();
        PEAR_ErrorStack::staticGetErrors(true);
    }

    function errorCodeToString($package, $code)
    {
        $codes = array(
           'PHP_Parser_DocBlock_DefaultLexer' =>
            array(
                PHP_PARSER_DOCLEX_ERROR_LEX => 'PHP_PARSER_DOCLEX_ERROR_LEX',
                PHP_PARSER_DOCLEX_ERROR_NUMWRONG => 'PHP_PARSER_DOCLEX_ERROR_NUMWRONG',
                PHP_PARSER_DOCLEX_ERROR_NODOT => 'PHP_PARSER_DOCLEX_ERROR_NODOT',
            ),
           'PHP_Parser_DocBlock_Default' =>
            array(
                PHP_PARSER_DOCBLOCK_DEFAULT_ERROR_PARSE => 'PHP_PARSER_DOCBLOCK_DEFAULT_ERROR_PARSE',
            ),
        );
        return $codes[$package][$code];
    }
    
    function assertErrors($errors, $method)
    {
        $compare = PEAR_ErrorStack::staticGetErrors(false, true);
        $save = $compare;
        foreach ($errors as $err) {
            foreach ($compare as $i => $guineapig) {
                if ($err['level'] == $guineapig['level'] &&
                      $err['package'] == $guineapig['package'] &&
                      $err['message'] == $guineapig['message'] &&
                      $err['code'] == $guineapig['code']) {
                    unset($compare[$i]);
                }
            }
        }
        $compare = array_values($compare);
        if (count($compare)) {
            foreach ($compare as $err) {
                $this->assertFalse(true, "$method Extra error: package $err[package], message '$err[message]', level $err[level], code " . $this->errorCodeToString($err['package'], $err['code']));
            }
        }
        foreach ($save as $err) {
            foreach ($errors as $i => $guineapig) {
                if ($err['level'] == $guineapig['level'] &&
                      $err['package'] == $guineapig['package'] &&
                      $err['message'] == $guineapig['message'] &&
                      $err['code'] == $guineapig['code']) {
                    unset($errors[$i]);
                }
            }
        }
        foreach ($errors as $err) {
            $this->assertFalse(true, "$method Unthrown error: package $err[package], message '$err[message]', level $err[level], code " . $this->errorCodeToString($err['package'], $err['code']));
        }
    }
    
    function assertNoErrors($method, $message)
    {
        $errs = PEAR_ErrorStack::staticGetErrors(false, true);
        $error = 'error';
        if (count($errs)) {
            if (count($errs) > 1) {
                $error .= 's';
            }
            $count = count($errs);
            $this->assertFalse(true, "$method $message triggered $count $error:");
            foreach ($errs as $error) {
                $this->assertFalse(true, $error['message']);
                $this->assertFalse(true, $this->errorCodeToString($error['package'],
                   $error['code']));
                $this->assertFalse(true, $error['level']);
            }
        }
        PEAR_ErrorStack::staticGetErrors(true);
    }

    function _stripWhitespace($str)
    {
        return preg_replace('/\\s+/', '', $str);
    }

    function _methodExists($name) 
    {
        $test = (version_compare(phpversion(), '4.3.5', '>') == 1) ? $name : strtolower($name);
        if (in_array($test, get_class_methods($this->parser))) {
            return true;
        }
        $this->assertTrue(false, 'method '. $name . ' not implemented in ' . get_class($this->parser));
        return false;
    }

    function errorHandler($errno, $errstr, $errfile, $errline) {
        if (error_reporting() == 0) {
            return;
        }
        //die("$errstr in $errfile at line $errline: $errstr");
        $this->errorOccured = true;
        $this->assertTrue(false, "$errstr at line $errline, $errfile");
    }
    
    function showNL($s)
    {
        return str_replace(array("\n","\r"),array('\n','\r'), $s);
    }

    function test_stripnonessentials_newlines()
    {
        if (!$this->_methodExists('stripNonEssentials')) {
            return;
        }
        $this->assertEquals($this->showNL("a\na"), $this->showNL($this->parser->stripNonEssentials("/**a\r\n*a*/")), 'newline 1');
        $this->assertEquals($this->showNL("a\na"), $this->showNL($this->parser->stripNonEssentials("/**a\n\r*a*/")), 'newline 2');
        $this->assertEquals($this->showNL("a\n\na"), $this->showNL($this->parser->stripNonEssentials("/**a\r\n*\r\n*a*/")), 'newline 3');
        $this->assertEquals($this->showNL("a\n\na"), $this->showNL($this->parser->stripNonEssentials("/**a\n\r*\n\r*a*/")), 'newline 4');
    }

    function test_stripnonessentials_basic()
    {
        if (!$this->_methodExists('stripNonEssentials')) {
            return;
        }
        $this->assertEquals('', $this->parser->stripNonEssentials('/***/'), '1');
        $this->assertEquals('', $this->parser->stripNonEssentials("/**\n*/"), '2');
        $this->assertEquals('', $this->parser->stripNonEssentials("/**#@+\n*/"), '3');
        $this->assertEquals('', $this->parser->stripNonEssentials("/**#@+*/"), '4');
    }
}
?>