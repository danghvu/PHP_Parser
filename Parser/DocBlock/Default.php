<?php
 // created by jay 0.8 (c) 1998 Axel.Schreiner@informatik.uni-osnabrueck.de
 // modified by alan@akbkhome.com to try to generate php!
 // modified by cellog@users.sourceforge.net to fit PEAR CS
 // %token constants

 require_once 'PEAR/ErrorStack.php';

 if (!defined('PHP_PARSER_ERROR_UNEXPECTED')) { define('PHP_PARSER_ERROR_UNEXPECTED', 1); }
 if (!defined('PHP_PARSER_ERROR_SYNTAX')) { define('PHP_PARSER_ERROR_SYNTAX', 2); }
 if (!defined('PHP_PARSER_ERROR_SYNTAX_EOF')) { define('PHP_PARSER_ERROR_SYNTAX_EOF', 3); }
if (!defined('TOKEN_yyErrorCode')) {   define('TOKEN_yyErrorCode', 256);
}
 // Class now

					// line 1 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"

?><?php
//
// +----------------------------------------------------------------------+
// | PHP_Parser                                                           |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2004 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 3.0 of the PHP license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available through the world-wide-web at the following url:           |
// | http://www.php.net/license/3_0.txt.                                  |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Greg Beaver <cellog@php.net>                                |
// +----------------------------------------------------------------------+
//
// $Id$
//
define('PHP_PARSER_DOCBLOCK_DEFAULT_ERROR_PARSE', 1);
require_once 'PHP/Parser/MsgServer.php';

/**
 * Default phpDocumentor DocBlock Parser
 * @package PHP_Parser
 */
class PHP_Parser_DocBlock_Default {

    /**
     * Options, used to control how the parser collects
     * and distributes the data it finds.
     *
     * Currently, options are grouped into two categories:
     * - containers for data
     * - publishing of data
     *
     * Default action is to return arrays of parsed data
     * for use by other applications.  The first set of
     * options, container options, provide a means to
     * tell the parser to encapsulate data in objects
     * instead of in arrays.  The option tells the parser which
     * class to instantiate for each documentable element.  The
     * default value of false will prompt the usage of arrays
     * instead.
     *
     * The second set of options provide for intermediary
     * publishing of data while parsing, to allow other
     * classes to hook into functionality if they desire
     * @access protected
     * @var array
     */
    var $_options = array();
    
    /**
     * The global message server
     * @var PHP_Parser_MsgServer
     */
    var $_server;
    
    /**
     * The error stack
     * @var PEAR_ErrorStack
     */
    var $_errorStack;
    
    /**
     * Tags from parsing
     * @tutorial tags.pkg
     * @var array
     */
    var $tags = array();
    
    /**
     * Long description
     * @var array
     */
    var $paragraphs = array();
    
    /**
     * Summary of documentation
     * @var array
     */
    var $summary = array();
    
    /**
     * Compatibility with PHP 4
     * @param array
     */
    function PHP_Parser_DocBlock_Default($options = array())
    {
        $this->_server = &PHP_Parser_MsgServer::singleton();
        $this->_errorStack = &PEAR_ErrorStack::singleton('PHP_Parser_DocBlock_Default');
        $this->_options['publishConstMessage'] =
        $this->_options['parseInternal'] =
        false;
        $this->_options['tagParserMap'] = array();
        $this->_options['inlineTagParserMap'] = array();
        $this->_options['docblockClass'] =
        $this->_options['completeTagClass'] =
        $this->_options['codeClass'] =
        $this->_options['preClass'] =
        $this->_options['boldClass'] =
        $this->_options['italicClass'] =
        $this->_options['varClass'] =
        $this->_options['kbdClass'] =
        $this->_options['sampClass'] =
        $this->_options['listClass'] =
        $this->_options['listitemClass'] =
        $this->_options['tagsContainerClass'] =
        false;
        $this->_options = array_merge($this->_options, $options);
        if (!class_exists($this->_options['tagsContainerClass'])) {
            $this->_options['tagsContainerClass'] = false;
        }
        if (!class_exists($this->_options['listClass'])) {
            $this->_options['listClass'] = false;
        }
        if (!class_exists($this->_options['listitemClass'])) { // until we can instanceof a classname
            $this->_options['listitemClass'] = false;
        }
        if (!class_exists($this->_options['codeClass'])) { // until we can instanceof a classname
            $this->_options['codeClass'] = false;
        }
        if (!class_exists($this->_options['completeTagClass'])) { // until we can instanceof a classname
            $this->_options['completeTagClass'] = false;
        }
        if (!class_exists($this->_options['docblockClass'])) { // until we can instanceof a classname
            $this->_options['codeClass'] = false;
        }
        if (!class_exists($this->_options['preClass'])) { // until we can instanceof a classname
            $this->_options['preClass'] = false;
        }
        if (!class_exists($this->_options['boldClass'])) { // until we can instanceof a classname
            $this->_options['boldClass'] = false;
        }
        if (!class_exists($this->_options['italicClass'])) { // until we can instanceof a classname
            $this->_options['italicClass'] = false;
        }
        if (!class_exists($this->_options['varClass'])) { // until we can instanceof a classname
            $this->_options['varClass'] = false;
        }
        if (!class_exists($this->_options['kbdClass'])) { // until we can instanceof a classname
            $this->_options['kbdClass'] = false;
        }
        if (!class_exists($this->_options['sampClass'])) { // until we can instanceof a classname
            $this->_options['sampClass'] = false;
        }
        if (is_array($this->_options['tagParserMap'])) {
            $map = $this->_options['tagParserMap'];
            foreach($map as $tag => $handler) {
                if (!is_a($handler, 'PHP_Parser_DocBlock_TagParser')) {
                    unset($this->_options['tagParserMap'][$tag]);
                }
            }
        }
        if (is_array($this->_options['inlineTagParserMap'])) {
            $map = $this->_options['inlineTagParserMap'];
            foreach($map as $tag => $handler) {
                if (!is_a($handler, 'PHP_Parser_DocBlock_InlineTagParser')) {
                    unset($this->_options['inlineTagParserMap'][$tag]);
                }
            }
        }
    }

    /**
     * global variable name of parser arrays
     * should match the build options  
     *
     * @var string
     * @access public 
     */
    var $yyGlobalName = '_PHP_PARSER_DOCBLOCK_DEFAULT';

    /**
     * (syntax) error message.
     * Can be overwritten to control message format.
     * @param message text to be displayed.
     * @param expected vector of acceptable tokens, if available.
     */
    function raiseError ($message, $code, $params)
    {     
        if (isset($params['expected'])) {
            $p = $params['expected'];
            $m = "$message, expecting ";
            if (count($p) - 1) {
                $last = array_pop($p);
                array_push($p, 'or ' . $last);
            }
            $m .= implode(', ', $p);
        } else {
            $m = $message;
        }
        return $this->_errorStack->push(
            PHP_PARSER_DOCBLOCK_DEFAULT_ERROR_PARSE,
            'error', $params,
            $m);  
    }
    
    function _newList($item, $lt)
    {
        $l = $this->_options['listClass'];
        $i = $this->_options['listitemClass'];
        if ($l && $i) {
            $list = new $l();
            $list->setType($lt);
            $list->addItem(new $i($item));
        } else {
            $list = array(
                        'type' => $lt,
                        'list' =>
                        array('items' => array($item)));
        }
        return $list;
    }
    
    function _addList($list, $item)
    {
        if (is_array($list)) {
            $list['items'][] = $item;
        } else {
            $i = $this->_options['listitemClass'];
            $list->addItem(new $i($item));
        }
        return $list;
    }
    
    /**
     * @param array $options
     * @param:array string $comment DocBlock to parse
     * @param:array integer $commentline line number
     * @param:array array $commenttoken T_DOC_COMMENT token
     * @param:array PHP_Parser_DocBlock_Lexer $lex DocBlock lexer
     * @param:array boolean $nosummary if true, then the description will not
     *                      be separated into summary/long description
     * @param:array boolean $preformatted if true, then the documentation
     *                      has already had the comment stuff stripped
     */
    function parse($options)
    {
        if (count($options) < 4) {
            return false;
        }
        $comment = $options['comment'];
        $line = @$options['commentline'];
        $token = @$options['commenttoken'];
        $lex = $options['lexer'];
        
        $this->summary = $this->paragraphs = $this->tags = array();

        $endlinenumber = $line + count(explode("\n", $comment));
        $dtemplate = false;
        if (!isset($options['tagdesc'])) {
            if ($comment == '/**#@-*/') {
                $parsed_docs = false;
                $this->_server->sendMessage(PHPDOCUMENTOR_PARSED_DOCTEMPLATE_STOP, false);
                return false;
            }
            if (strpos($comment,'/**#@+') === 0) {
                $dtemplate = true;
            }
            $comment = $this->stripNonEssentials($comment);
        }
        $lex->setup($comment);
        $result = $this->yyparse($lex);
        if (PEAR::isError($result)) {
            echo $result->getMessage()."\n";
            return $result;
        }
        if (!isset($options['nosummary'])) {
            $this->setSummary();
        }
        $docblock = $this->_options['docblockClass'];
        if ($docblock) {
            $parsed_docs = new $docblock($this);
            $parsed_docs->setStartLine($line);
            $parsed_docs->setEndLine($endlinenumber);
        } else {
            $parsed_docs =
                array(
                    'summary' => $this->summary,
                    'documentation' => $this->paragraphs,
                    'tags' => $this->tags,
                    'startline' => $line,
                    'endline' => $endlinenumber,
                     );
        }
        if ($dtemplate) {
            $this->_server->sendMessage('parsed docblock template', $parsed_docs);
        } else {
            if (!isset($options['tagdesc'])) {
                $this->_server->sendMessage('parsed docblock', $parsed_docs);
            }
            return $parsed_docs;
        }
    }

    /**
     * Extract the summary from the description, and set it.
     *
     * This can be overridden in child classes to do other methods of
     * summary extraction, such as the doxygen method of extracting
     * a certain number of characters, or Javadoc's method of extracting
     * to the first period
     */
    function setSummary()
    {
        if (!isset($this->paragraphs[0])) {
            return;
        }
        $this->summary = $this->paragraphs[0];
        $lineindex = 0;
        $nlcount = 0;
        $oldnlcount = 0;
        $retsummary = $retdescription = array();
        foreach($this->summary as $i => $item) {
            $oldnlcount = $nlcount;
            if (is_array($item)) {
                // no way to calculate arrays since they can be nested
                $retsummary = array_slice($this->summary, 0, $i);
                $retdescription = array_slice($this->summary, $i);
                $this->summary = $retsummary;
                $this->paragraphs[0] = $retdescription;
                return;
            }
            if (is_object($item)) {
                if ((method_exists($item, 'hasmultiplecr') && $item->hasMultipleCR())
                        || is_a($item, 'PHP_Parser_DocBlock_List')) {
                    $retsummary = array_slice($this->summary, 0, $i);
                    $retdescription = array_slice($this->summary, $i);
                    $this->summary = $retsummary;
                    $this->paragraphs[0] = $retdescription;
                    return;
                }
                // all other objects can't contain \n
                continue;
            }
            if (count(explode("\n\n", $item)) - 1) {
                // contains a double newline - this is it
                $summary = array_shift($a = explode("\n\n", $item));
                $description = join($a);
                $retsummary[$i] = $summary;
                break;
            }
            if (count($a = explode("\n", $item)) - 1) {
                $nlcount += count($a) - 1;
                // contains newlines
                if ($nlcount > 3) {
                    // we've found our summary in this block
                    if ($oldnlcount == 2) {
                        $retsummary = array_slice($this->summary, 0, $i);
                        $retsummary[] = array_shift($a);
                        $retdescription = array_merge(array(join($a, "\n")),
                                                      array_slice($this->summary, $i + 1));
                    }
                    if ($oldnlcount == 3) {
                        $retsummary = array_slice($this->summary, 0, $i - 1);
                        $retdescription = array_slice($this->summary, $i - 1);
                    }
                    $this->summary = $retsummary;
                    $this->paragraphs[0] = $retdescription;
                    return;
                }
            }
        }
        if (isset($description)) {
            for($j = 0; $j < $i; $j++) {
                $retsummary[$j] = $this->summary[$i];
            }
            $retdescription = array($description);
            for($j = $i; $j < count($this->summary); $j++) {
                $retdescription[] = $this->summary[$i];
            }
            $this->summary = $retsummary;
            $this->paragraphs[0] = $retdescription;
            return;
        }
        
        
        unset($this->paragraphs[0]);
        $this->paragraphs = array_values($this->paragraphs);
    }

    function getSummary()
    {
        return $this->summary;
    }
    
    function getDescription()
    {
        return $this->paragraphs;
    }
    
    function getTags()
    {
        return $this->tags;
    }
    
    /**
     * Remove the /**, * from the doc comment
     *
     * Also remove blank lines
     * @param string
     * @return array
     */
    function stripNonEssentials($comment)
    {
        $comment = str_replace("\r\n", "\n", trim($comment));
        $comment = str_replace("\n\r", "\n", trim($comment));
        if (strpos($comment, '/**#@+') === 0)
        { // docblock template definition
            // strip /**#@+ and */
            $comment = substr($comment,6).'*';
            $comment = substr($comment,0,strlen($comment) - 2);
        } else
        {
            // strip /** and */
            $comment = substr($comment,2);
            $comment = substr($comment,0,strlen($comment) - 2);
        }
        $lines = explode("\n", trim($comment));
        $go = count($lines);
        for($i=0; $i < $go; $i++)
        {
            if (substr(trim($lines[$i]),0,1) != '*') {
                unset($lines[$i]);
            } else {
                $lines[$i] = substr(trim($lines[$i]),1); // remove leading "* "
            }
        }
        // remove empty lines
        return trim(join("\n", $lines));
    }

    function _parseTag($name, $contents)
    {
        if (is_array($this->_options['tagParserMap'])) {
            if (isset($this->_options['tagParserMap']
                  [str_replace('@', '', $name)])) {
                // use custom tag parser
                return $this->_options['tagParserMap'][str_replace('@', '',
                  $name)]->parseTag(str_replace('@', '', $name), $contents);
            } elseif (isset($this->_options['tagParserMap']['*'])) {
                // use default tag parser
                return $this->_options['tagParserMap']['*']->parseTag(
                  str_replace('@', '', $name), $contents);
            } else {
                // no default handler
                return array('tag' => str_replace('@', '', $name),
                  'value' => $contents);
            }
        } else {
            // no registered tag parsers
            return array('tag' => str_replace('@', '', $name),
              'value' => $contents);
        }
    }
    
    function _parseInlineTag($name, $contents)
    {
        if (is_array($this->_options['inlineTagParserMap'])) {
            if (isset($this->_options['inlineTagParserMap'][$name])) {
                // use custom inline tag parser
                return $this->_options['inlineTagParserMap']
                  [$name]->parseInlineTag($name, $contents);
            } elseif (isset($this->_options['inlineTagParserMap']['*'])) {
                // use default inline tag parser
                return $this->_options['inlineTagParserMap']
                  ['*']->parseInlineTag($name, $contents);
            } else {
                // no default handler
                return array('inlinetag' => $name, 'value' => $contents);
            }
        } else {
            // no registered inline tag parsers
            return array('inlinetag' => $name, 'value' => $contents);
        }
    }
					// line 498 "-"

    /**
     * thrown for irrecoverable syntax errors and stack overflow.
     */
    
     var $yyErrorCode = 256;

    /**
     * Debugging
     */
     var $debug = false;




    /**
     * index-checked interface to yyName[].
     * @param token single character or %token value.
     * @return token name or [illegal] or [unknown].
     */
    function yyname ($token) {
        if ($token < 0 || $token >  count($GLOBALS[$this->yyGlobalName]['yyName'])) return "[illegal]";
        if (($name = $GLOBALS[$this->yyGlobalName]['yyName'][$token]) != null) return $name;
        return "[unknown]";
    }

    /**
     * computes list of expected tokens on error by tracing the tables.
     * @param state for which to compute the list.
     * @return list of token names.
     */
    function yyExpecting ($state) {
        $len = 0;
        $ok = array();//new boolean[YyNameClass.yyName.length];

        if (($n =  $GLOBALS[$this->yyGlobalName]['yySindex'][$state]) != 0) {
            $start = 1;
            for ($token = $start;
                $token < count($GLOBALS[$this->yyGlobalName]['yyName']) && 
                        $n+$token < count($GLOBALS[$this->yyGlobalName]['yyTable']); $token++) {
                if (@$GLOBALS[$this->yyGlobalName]['yyCheck'][$n+$token] == $token && !@$ok[$token] && 
                        $GLOBALS[$this->yyGlobalName]['yyName'][$token] != null) {
                    $len++;
                    $ok[$token] = true;
                }
            } // end for
        }
        if (($n = $GLOBALS[$this->yyGlobalName]['yyRindex'][$state]) != 0) {
            $start = 1;
            for ($token = $start;
                     $token < count($GLOBALS[$this->yyGlobalName]['yyName'])  && 
                     $n+$token <  count($GLOBALS[$this->yyGlobalName]['yyTable']); $token++) 
            {
               if (@$GLOBALS[$this->yyGlobalName]['yyCheck'][$n+$token] == $token && !@$ok[$token] 
                          && @$GLOBALS[$this->yyGlobalName]['yyName'][$token] != null) {
                    $len++;
                    $ok[$token] = true;
               }
            } // end for
        }
        $result = array();
        for ($n = $token = 0; $n < $len;  $token++) {
            if (@$ok[$token]) { $result[$n++] =$GLOBALS[$this->yyGlobalName]['yyName'][$token]; }
        }
        return $result;
    }


    /**
     * initial size and increment of the state/value stack [default 256].
     * This is not final so that it can be overwritten outside of invocations
     * of yyparse().
     */
    var $yyMax;

    /**
     * executed at the beginning of a reduce action.
     * Used as $$ = yyDefault($1), prior to the user-specified action, if any.
     * Can be overwritten to provide deep copy, etc.
     * @param first value for $1, or null.
     * @return first.
     */
    function yyDefault ($first) {
        return $first;
    }

    /**
     * the generated parser.
     * Maintains a state and a value stack, currently with fixed maximum size.
     * @param yyLex scanner.
     * @return result of the last reduction, if any.
     * @throws yyException on irrecoverable parse error.
     */
    function yyparse (&$yyLex) {
        $this->debug = true;
        $this->yyLex = &$yyLex;
        if (!$this->yyGlobalName) {
            echo "\n\nYou must define \$this->yyGlobalName to match the build option -g _XXXXX \n\n";
            exit;
        }
        if ($this->debug)
           echo "\tStarting jay:yyparse";
        //error_reporting(E_ALL);
        if ($this->yyMax <= 0) $this->yyMax = 256;			// initial size
        $this->yyState = 0;
        $this->yyStates = array();
        $this->yyVal = null;
        $this->yyValWithWhitespace = null;
        $this->yyVals = array();
        $this->yW = array();
        $yyTableCount = count($GLOBALS[$this->yyGlobalName]['yyTable']);
        $yyToken = -1;                 // current input
        $yyErrorFlag = 0;              // #tks to shift
        $tloop = 0;
    
        while (1) {//yyLoop: 
            //echo "yyLoop\n";
            //if ($this->debug) echo "\tyyLoop:\n";
            for ($yyTop = 0;; $yyTop++) {
                //if ($this->debug) echo ($tloop++) .">>>>>>yyLoop:yTop = {$yyTop}\n";
                $this->yyStates[$yyTop] = $this->yyState;
                $this->yyVals[$yyTop] = $this->yyVal;
                $this->yW[$yyTop] = $this->yyValWithWhitespace;

                //yyDiscarded: 
                for (;;) {	// discarding a token does not change stack
                    //echo "yyDiscarded\n";
                    if ($this->debug) echo "\tIn main loop : State = {$this->yyState}\n";
                    if ($this->debug) echo "\tyydefred = {$GLOBALS[$this->yyGlobalName]['yyDefRed'][$this->yyState]}\n";
                    if (($yyN = $GLOBALS[$this->yyGlobalName]['yyDefRed'][$this->yyState]) == 0) {	
                        // else [default] reduce (yyN)
                        //if ($this->debug) echo "\tA:token is $yyToken\n";
                        if ($yyToken < 0) {
                            //if ($this->debug) echo "\tA:advance\n";
                            if ($yyLex->advance()) {
                               
                                $yyToken = $yyLex->token ;
                            } else {
                                $yyToken = 0;
                            }
                        }
                        if ($this->debug) {
                            echo "\tA:token is now " .
                            "{$GLOBALS[$this->yyGlobalName]['yyName'][$yyToken]} " .token_name($yyToken).  "\n";
                            var_dump($yyToken);
                        }
                        //if ($this->debug) echo "GOT TOKEN $yyToken";
                        //if ($this->debug) echo "Sindex:  {$GLOBALS[$this->yyGlobalName]['yySindex'][$this->yyState]}\n";

                        if (($yyN = $GLOBALS[$this->yyGlobalName]['yySindex'][$this->yyState]) != 0
                                  && ($yyN += $yyToken) >= 0
                                  && $yyN < $yyTableCount && $GLOBALS[$this->yyGlobalName]['yyCheck'][$yyN] == $yyToken) {
                            $this->yyState = $GLOBALS[$this->yyGlobalName]['yyTable'][$yyN];		// shift to yyN
                            $this->yyVal = $yyLex->value;
                            $this->yyValWithWhitespace = $yyLex->valueWithWhitespace;
                            $yyToken = -1;
                            if ($yyErrorFlag > 0) $yyErrorFlag--;
                            continue 2; // goto!!yyLoop;
                        }
 
                       
              
                        if (($yyN = $GLOBALS[$this->yyGlobalName]['yyRindex'][$this->yyState]) != 0
                                && ($yyN += $yyToken) >= 0
                                && $yyN < $yyTableCount && $GLOBALS[$this->yyGlobalName]['yyCheck'][$yyN] == $yyToken) {
                            $yyN = $GLOBALS[$this->yyGlobalName]['yyTable'][$yyN];			// reduce (yyN)
                        } else {
                            switch ($yyErrorFlag) {
    
                                case 0:
                                    $data = $yyLex->parseError();
                                    $info = $data[0];
                                    $info .= ', Unexpected '.$this->yyName($yyToken).',';
                                    return $this->raiseError("$info syntax error",
                                                PHP_PARSER_ERROR_UNEXPECTED,
                                                array(
                                                  'expected' => $this->yyExpecting($this->yyState),
                                                  'token' => $this->yyName($yyToken),
                                                  'line' => $data[1],
                                                ));
                                
                                case 1: case 2:
                                    $yyErrorFlag = 3;
                                    do { 
                                        if (($yyN = @$GLOBALS[$this->yyGlobalName]['yySindex']
                                                [$this->yyStates[$yyTop]]) != 0
                                                && ($yyN += $this->yyErrorCode) >= 0 && $yyN < $yyTableCount
                                                && $GLOBALS[$this->yyGlobalName]['yyCheck'][$yyN] == $this->yyErrorCode) {
                                            $this->yyState = $GLOBALS[$this->yyGlobalName]['yyTable'][$yyN];
                                            $this->yyVal = $yyLex->value;
                                            $this->yyValWithWhitespace = $yyLex->valueWithWhitespace;
                                            //vi /echo "goto yyLoop?\n";
                                            break 3; //continue yyLoop;
                                        }
                                    } while ($yyTop-- >= 0);
                                    $data = $yyLex->parseError();
                                    return $this->raiseError("$data[0] irrecoverable syntax error",
                                           PHP_PARSER_ERROR_SYNTAX,
                                           array('line' => $data[1]));
    
                                case 3:
                                    if ($yyToken == 0) {
                                        $info =$yyLex->parseError();
                                        return $this->raiseError("$info[0] irrecoverable syntax error at end-of-file",
                                           PHP_PARSER_ERROR_SYNTAX_EOF,
                                           array('line' => $info[1]));
                                    }
                                    $yyToken = -1;
                                    //echo "goto yyDiscarded?";  
                                    break 1; //continue yyDiscarded;		// leave stack alone
                            }
                        }
                    }    
                    $yyV = $yyTop + 1-$GLOBALS[$this->yyGlobalName]['yyLen'][$yyN];
                    //if ($this->debug) echo "\tyyV is $yyV\n";
                    $yyVal = $yyV > $yyTop ? null : $this->yyVals[$yyV];
                    //if ($this->debug) echo "\tyyVal is ". serialize($yyVal) ."\n";
                    if ($this->debug) echo "\tswitch($yyN)\n";
                   
                    $method = '_' .$yyN;
                    if (method_exists($this,$method)) {
                         $this->$method($yyTop);

                    }
                   
                    //if ($this->debug) echo "\tDONE switch\n";if ($this->debug) echo "\t--------------\n";
                    $yyTop -= $GLOBALS[$this->yyGlobalName]['yyLen'][$yyN];
                    //if ($this->debug) echo "\tyyTop is $yyTop\n";
                    $this->yyState = $this->yyStates[$yyTop];
                    //if ($this->debug) echo "\tyyState is {$this->yyState}\n";
                    $yyM = $GLOBALS[$this->yyGlobalName]['yyLhs'][$yyN];
                    //if ($this->debug) echo "\tyyM is now $yyM\n";



                    if ($this->yyState == 0 && $yyM == 0) {
                        $this->yyState = $GLOBALS[$this->yyGlobalName]['yyFinal'];
                        if ($yyToken < 0) {
                            $yyToken =0;
                            if ($yyLex->advance()) {
                                $yyToken = $yyLex->token;
                            }
                        }
                        if ($this->debug) echo "\tTOKEN IS NOW $yyToken\n";
                        if ($yyToken == 0) {
                            return $yyVal;
                        }
                        //if ($this->debug) echo "\t>>>>> yyLoop(A)?\n";
                        continue 2; //continue yyLoop;
                    }
                    if (($yyN = $GLOBALS[$this->yyGlobalName]['yyGindex'][$yyM]) != 0 && ($yyN += $this->yyState) >= 0
                            && $yyN < $yyTableCount && $GLOBALS[$this->yyGlobalName]['yyCheck'][$yyN] == $this->yyState) {
                        //if ($this->debug) echo "\tyyState: using yyTable\n";
                        $this->yyState = $GLOBALS[$this->yyGlobalName]['yyTable'][$yyN];
                    } else {
                        //if ($this->debug) echo "\tyyState: using yyDgoto\n";
                        $this->yyState = $GLOBALS[$this->yyGlobalName]['yyDgoto'][$yyM];
                    }  
                    //if ($this->debug) echo "\t>>>>> yyLoop(B)?\n";
                    continue 2;//continue yyLoop;
                }
            }
        }
    }


    function _1($yyTop)  					// line 537 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->paragraphs = array($this->yyVals[0+$yyTop]);
        }

    function _2($yyTop)  					// line 541 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->paragraphs = $this->yyVals[-1+$yyTop];
            $this->tags = $this->yyVals[0+$yyTop];
        }

    function _3($yyTop)  					// line 546 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            array_unshift($this->yyVals[-1+$yyTop], $this->yyVals[-2+$yyTop]);
            $this->paragraphs = $this->yyVals[-1+$yyTop];
            $this->tags = $this->yyVals[0+$yyTop];
        }

    function _4($yyTop)  					// line 552 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            array_unshift($this->yyVals[0+$yyTop], $this->yyVals[-1+$yyTop]);
            $this->paragraphs = $this->yyVals[0+$yyTop];
        }

    function _5($yyTop)  					// line 557 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->paragraphs = $this->yyVals[-1+$yyTop];
            $this->tags = $this->yyVals[0+$yyTop];
        }

    function _6($yyTop)  					// line 562 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->tags = $this->yyVals[0+$yyTop];
        }

    function _7($yyTop)  					// line 570 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
        $this->yyVal = $this->yyVals[0+$yyTop];
    }

    function _8($yyTop)  					// line 577 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->tags[] = $this->_parseTag($this->yyVals[0+$yyTop], array());
        }

    function _9($yyTop)  					// line 581 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->tags[] = $this->_parseTag($this->yyVals[-1+$yyTop], $this->yyVals[0+$yyTop]);
        }

    function _10($yyTop)  					// line 585 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            if (is_string($this->yyVals[0+$yyTop][0])) {
                $this->yyVals[0+$yyTop][0] = $this->yyVals[-1+$yyTop] . $this->yyVals[0+$yyTop][0];
            }
            $this->tags[] = $this->_parseTag($this->yyVals[-2+$yyTop], $this->yyVals[0+$yyTop]);
        }

    function _12($yyTop)  					// line 595 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->yyVal = array($this->yyVals[0+$yyTop]);
        }

    function _13($yyTop)  					// line 599 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->yyVal = $this->yyVals[-2+$yyTop];
            $this->yyVal[] = $this->yyVals[0+$yyTop];
        }

    function _14($yyTop)  					// line 607 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->yyVal = array($this->yyVals[0+$yyTop]);
        }

    function _15($yyTop)  					// line 611 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->yyVal = $this->yyVals[-1+$yyTop];
            $this->yyVal[] = $this->yyVals[0+$yyTop];
        }

    function _16($yyTop)  					// line 619 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->yyVal = array($this->yyVals[0+$yyTop]);
        }

    function _17($yyTop)  					// line 623 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->yyVal = array($this->yyVals[0+$yyTop]);
        }

    function _18($yyTop)  					// line 627 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->yyVal = array($this->yyVals[0+$yyTop]);
        }

    function _19($yyTop)  					// line 631 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->yyVal = array($this->yyVals[0+$yyTop]);
        }

    function _20($yyTop)  					// line 635 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->yyVal = array($this->yyVals[0+$yyTop]);
        }

    function _21($yyTop)  					// line 639 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->yyVal = str_replace(array('<<', '>>', '/'), array('<', '>', ''), $this->yyVals[0+$yyTop]);
        }

    function _22($yyTop)  					// line 643 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->yyVal = array($this->yyVals[0+$yyTop]);
        }

    function _23($yyTop)  					// line 647 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            if ($this->yyVals[0+$yyTop] == '{@}') {
                $this->yyVal = array('{@');
            } elseif ($this->yyVals[0+$yyTop] == '{@*}') {
                $this->yyVal = array('*/');
            } else {
                $this->yyVal = array('');
            }
        }

    function _24($yyTop)  					// line 657 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->yyVal = $this->yyVals[-1+$yyTop];
            $index = count($this->yyVal) - 1;
            if (is_string($this->yyVal[$index])) {
                $this->yyVal[$index] .= $this->yyVals[0+$yyTop];
            } else {
                $this->yyVal[] = $this->yyVals[0+$yyTop];
            }
        }

    function _25($yyTop)  					// line 667 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->yyVal = $this->yyVals[-1+$yyTop];
            $this->yyVal[] = $this->yyVals[0+$yyTop];
        }

    function _26($yyTop)  					// line 672 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->yyVal = $this->yyVals[-1+$yyTop];
            $this->yyVal[] = $this->yyVals[0+$yyTop];
        }

    function _27($yyTop)  					// line 677 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->yyVal = $this->yyVals[-1+$yyTop];
            $this->yyVal[] = $this->yyVals[0+$yyTop];
        }

    function _28($yyTop)  					// line 682 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->yyVal = $this->yyVals[-1+$yyTop];
            $index = count($this->yyVal) - 1;
            if (is_string($this->yyVal[$index])) {
                $this->yyVal[$index] .= str_replace(array('<<', '>>', '/'), array('<', '>', ''), $this->yyVals[0+$yyTop]);
            } else {
                $this->yyVal[] = str_replace(array('<<', '>>', '/'), array('<', '>', ''), $this->yyVals[0+$yyTop]);
            }
        }

    function _29($yyTop)  					// line 692 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->yyVal = $this->yyVals[-1+$yyTop];
            $index = count($this->yyVal) - 1;
            if (is_string($this->yyVal[$index])) {
                $this->yyVal[$index] .= $this->yyVals[0+$yyTop];
            } else {
                $this->yyVal[] = $this->yyVals[0+$yyTop];
            }
        }

    function _30($yyTop)  					// line 702 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->yyVal = $this->yyVals[-1+$yyTop];
            $index = count($this->yyVal) - 1;
            if (is_string($this->yyVal[$index])) {
                $this->yyVal[$index] .= $this->yyVals[0+$yyTop];
            } else {
                $this->yyVal[] = $this->yyVals[0+$yyTop];
            }
        }

    function _31($yyTop)  					// line 712 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            if ($this->yyVals[0+$yyTop] == '{@}') {
                $temp = '{@';
            } elseif ($this->yyVals[0+$yyTop] == '{@*}') {
                $temp = '*/';
            } else {
                $temp = '';
            }
            $this->yyVal = $this->yyVals[-1+$yyTop];
            $index = count($this->yyVal) - 1;
            if (is_string($this->yyVal[$index])) {
                $this->yyVal[$index] .= $temp;
            } else {
                $this->yyVal[] = $temp;
            }
        }

    function _32($yyTop)  					// line 729 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->yyVal = $this->yyVals[-1+$yyTop];
            $this->yyVal[] = $this->yyVals[0+$yyTop];
        }

    function _33($yyTop)  					// line 737 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->yyVal = array($this->yyVals[0+$yyTop]);
        }

    function _34($yyTop)  					// line 741 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->yyVal = array($this->yyVals[0+$yyTop]);
        }

    function _35($yyTop)  					// line 745 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->yyVal = array($this->yyVals[0+$yyTop]);
        }

    function _36($yyTop)  					// line 749 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->yyVal = array($this->yyVals[0+$yyTop]);
        }

    function _37($yyTop)  					// line 753 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->yyVal = array($this->yyVals[0+$yyTop]);
        }

    function _38($yyTop)  					// line 757 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->yyVal = str_replace(array('<<', '>>', '/'), array('<', '>', ''), $this->yyVals[0+$yyTop]);
        }

    function _39($yyTop)  					// line 761 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            if ($this->yyVals[0+$yyTop] == '{@}') {
                $this->yyVal = array('{@');
            } elseif ($this->yyVals[0+$yyTop] == '{@*}') {
                $this->yyVal = array('*/');
            } else {
                $this->yyVal = array('');
            }
        }

    function _40($yyTop)  					// line 771 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->yyVal = array($this->yyVals[0+$yyTop]);
        }

    function _41($yyTop)  					// line 775 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->yyVal = $this->yyVals[-1+$yyTop];
            $index = count($this->yyVal) - 1;
            if (is_string($this->yyVal[$index])) {
                $this->yyVal[$index] .= $this->yyVals[0+$yyTop];
            } else {
                $this->yyVal[] = $this->yyVals[0+$yyTop];
            }
        }

    function _42($yyTop)  					// line 785 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->yyVal = $this->yyVals[-1+$yyTop];
            $this->yyVal[] = $this->yyVals[0+$yyTop];
         }

    function _43($yyTop)  					// line 790 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->yyVal = $this->yyVals[-1+$yyTop];
            $this->yyVal[] = $this->yyVals[0+$yyTop];
        }

    function _44($yyTop)  					// line 795 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->yyVal = $this->yyVals[-1+$yyTop];
            $this->yyVal[] = $this->yyVals[0+$yyTop];
        }

    function _45($yyTop)  					// line 800 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->yyVal = $this->yyVals[-1+$yyTop];
            $this->yyVal[] = $this->yyVals[0+$yyTop];
        }

    function _46($yyTop)  					// line 805 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->yyVal = $this->yyVals[-1+$yyTop];
            $index = count($this->yyVal) - 1;
            if (is_string($this->yyVal[$index])) {
                $this->yyVal[$index] .= str_replace(array('<<', '>>', '/'), array('<', '>', ''), $this->yyVals[0+$yyTop]);
            } else {
                $this->yyVal[] = str_replace(array('<<', '>>', '/'), array('<', '>', ''), $this->yyVals[0+$yyTop]);
            }
        }

    function _47($yyTop)  					// line 815 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            if ($this->yyVals[0+$yyTop] == '{@}') {
                $t = array('{@');
            } elseif ($this->yyVals[0+$yyTop] == '{@*}') {
                $t = array('*/');
            } else {
                $t = array('');
            }
            $index = count($this->yyVal) - 1;
            if (is_string($this->yyVal[$index])) {
                $this->yyVal[$index] .= $t;
            } else {
                $this->yyVal[] = $t;
            }
        }

    function _48($yyTop)  					// line 831 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->yyVal = $this->yyVals[-1+$yyTop];
            $index = count($this->yyVal) - 1;
            if (is_string($this->yyVal[$index])) {
                $this->yyVal[$index] .= $this->yyVals[0+$yyTop];
            } else {
                $this->yyVal[] = $this->yyVals[0+$yyTop];
            }
        }

    function _49($yyTop)  					// line 844 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->yyVal = $this->yyVals[-1+$yyTop];
        }

    function _50($yyTop)  					// line 848 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->yyVal = $this->yyVals[-2+$yyTop];
        }

    function _51($yyTop)  					// line 852 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->yyVal = $this->yyVals[0+$yyTop];
        }

    function _52($yyTop)  					// line 859 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $tag = $this->_options['completeTagClass'];
            if ($tag) {
                $this->yyVal = new $tag($this->yyVals[0+$yyTop]);
            } else {
                $this->yyVal = array('completetag' => $this->yyVals[0+$yyTop]);
            }
        }

    function _59($yyTop)  					// line 877 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $tag = $this->_options['completeTagClass'];
            if ($tag) {
                $this->yyVal = new $tag($this->yyVals[0+$yyTop]);
            } else {
                $this->yyVal = array('completetag' => $this->yyVals[0+$yyTop]);
            }
        }

    function _66($yyTop)  					// line 895 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $tag = $this->_options['boldClass'];
            if ($tag) {
                $this->yyVal = new $tag($this->yyVals[-1+$yyTop]);
            } else {
                $this->yyVal = array('strong' => $this->yyVals[-1+$yyTop]);
            }
        }

    function _67($yyTop)  					// line 907 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $tag = $this->_options['boldClass'];
            if ($tag) {
                $this->yyVal = new $tag($this->yyVals[-1+$yyTop]);
            } else {
                $this->yyVal = array('strong' => $this->yyVals[-1+$yyTop]);
            }
        }

    function _68($yyTop)  					// line 919 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $tag = $this->_options['codeClass'];
            if ($tag) {
                $this->yyVal = new $tag($this->yyVals[-1+$yyTop]);
            } else {
                $this->yyVal = array('code' => $this->yyVals[-1+$yyTop]);
            }
        }

    function _69($yyTop)  					// line 931 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $tag = $this->_options['codeClass'];
            if ($tag) {
                $this->yyVal = new $tag($this->yyVals[-1+$yyTop]);
            } else {
                $this->yyVal = array('code' => $this->yyVals[-1+$yyTop]);
            }
        }

    function _70($yyTop)  					// line 943 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $tag = $this->_options['sampClass'];
            if ($tag) {
                $this->yyVal = new $tag($this->yyVals[-1+$yyTop]);
            } else {
                $this->yyVal = array('samp' =>  $this->yyVals[-1+$yyTop]);
            }
        }

    function _71($yyTop)  					// line 955 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $tag = $this->_options['sampClass'];
            if ($tag) {
                $this->yyVal = new $tag($this->yyVals[-1+$yyTop]);
            } else {
                $this->yyVal = array('samp' =>  $this->yyVals[-1+$yyTop]);
            }
        }

    function _72($yyTop)  					// line 967 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $tag = $this->_options['kbdClass'];
            if ($tag) {
                $this->yyVal = new $tag($this->yyVals[-1+$yyTop]);
            } else {
                $this->yyVal = array('kbd' =>  $this->yyVals[-1+$yyTop]);
            }
        }

    function _73($yyTop)  					// line 979 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $tag = $this->_options['kbdClass'];
            if ($tag) {
                $this->yyVal = new $tag($this->yyVals[-1+$yyTop]);
            } else {
                $this->yyVal = array('kbd' =>  $this->yyVals[-1+$yyTop]);
            }
        }

    function _74($yyTop)  					// line 991 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $tag = $this->_options['varClass'];
            if ($tag) {
                $this->yyVal = new $tag($this->yyVals[-1+$yyTop]);
            } else {
                $this->yyVal = array('var' =>  $this->yyVals[-1+$yyTop]);
            }
        }

    function _75($yyTop)  					// line 1003 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $tag = $this->_options['varClass'];
            if ($tag) {
                $this->yyVal = new $tag($this->yyVals[-1+$yyTop]);
            } else {
                $this->yyVal = array('var' =>  $this->yyVals[-1+$yyTop]);
            }
        }

    function _76($yyTop)  					// line 1015 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $list = $this->_options['listClass'];
            if ($list) {
                $this->yyVal = new $list($this->yyVals[-1+$yyTop], $this->yyVals[-2+$yyTop]);
            } else {
                $this->yyVal = array('list' => $this->yyVals[-1+$yyTop], 'type' => $this->yyVals[-2+$yyTop]);
            }
        }

    function _77($yyTop)  					// line 1027 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $list = $this->_options['listClass'];
            if ($list) {
                $this->yyVal = new $list(2);
            } else {
                $this->yyVal = array('list' => $this->yyVals[-1+$yyTop]);
            }
        }

    function _78($yyTop)  					// line 1039 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->yyVal = array($this->yyVals[0+$yyTop]);
        }

    function _79($yyTop)  					// line 1043 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->yyVal = $this->yyVals[-1+$yyTop];
            $this->yyVal[] = $this->yyVals[0+$yyTop];
        }

    function _80($yyTop)  					// line 1051 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->yyVal = array($this->yyVals[0+$yyTop]);
        }

    function _81($yyTop)  					// line 1055 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->yyVal = $this->yyVals[-1+$yyTop];
            $this->yyVal[] = $this->yyVals[0+$yyTop];
        }

    function _82($yyTop)  					// line 1063 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->yyVal = $this->yyVals[-1+$yyTop];
        }

    function _83($yyTop)  					// line 1070 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->yyVal = $this->yyVals[-1+$yyTop];
        }

    function _84($yyTop)  					// line 1077 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->yyVal = $this->yyVals[0+$yyTop];
        }

    function _85($yyTop)  					// line 1081 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->yyVal = $this->yyVals[-2+$yyTop];
        }

    function _86($yyTop)  					// line 1088 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->yyVal = $this->yyVals[0+$yyTop];
        }

    function _87($yyTop)  					// line 1092 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->yyVal = $this->yyVals[-2+$yyTop];
        }

    function _88($yyTop)  					// line 1099 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->yyVal = array('list' => array($this->yyVals[-1+$yyTop]), 'type' => $this->yyVals[-2+$yyTop]);
        }

    function _89($yyTop)  					// line 1103 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->yyVal = $this->yyVals[-3+$yyTop];
            $this->yyVal['list'][] = $this->yyVals[-1+$yyTop];
        }

    function _90($yyTop)  					// line 1110 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->yyVal = array('list' => array($this->yyVals[-1+$yyTop]), 'type' => $this->yyVals[-2+$yyTop]);
        }

    function _91($yyTop)  					// line 1114 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->yyVal = $this->yyVals[-3+$yyTop];
            $this->yyVal['list'][] = $this->yyVals[-1+$yyTop];
        }

    function _95($yyTop)  					// line 1129 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->yyVal = $this->yyVals[0+$yyTop];
        }

    function _96($yyTop)  					// line 1133 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->yyVal = $this->yyVals[0+$yyTop];
        }

    function _97($yyTop)  					// line 1140 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->yyVal = 'unordered';
        }

    function _98($yyTop)  					// line 1144 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->yyVal = 'ordered';
        }

    function _99($yyTop)  					// line 1148 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->yyVal = 'ordered';
        }

    function _100($yyTop)  					// line 1155 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->yyVal = 'unordered';
        }

    function _101($yyTop)  					// line 1159 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->yyVal = 'ordered';
        }

    function _102($yyTop)  					// line 1163 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->yyVal = 'ordered';
        }

    function _103($yyTop)  					// line 1171 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->yyVal = array($this->yyVals[0+$yyTop]);
        }

    function _104($yyTop)  					// line 1175 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->yyVal = array(str_replace(array('<<', '>>', '/'), array('<', '>', ''), $this->yyVals[0+$yyTop]));
        }

    function _105($yyTop)  					// line 1179 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->yyVal = array($this->yyVals[0+$yyTop]);
        }

    function _106($yyTop)  					// line 1183 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            if ($this->yyVals[0+$yyTop] == '{@}') {
                $this->yyVal = array('{@');
            } elseif ($this->yyVals[0+$yyTop] == '{@*}') {
                $this->yyVal = array('*/');
            } else {
                $this->yyVal = array('');
            }
        }

    function _107($yyTop)  					// line 1193 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->yyVal = array($this->yyVals[0+$yyTop]);
        }

    function _108($yyTop)  					// line 1197 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->yyVal = array($this->yyVals[0+$yyTop]);
        }

    function _109($yyTop)  					// line 1201 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->yyVal = $this->yyVals[-1+$yyTop];
            $this->yyVal[] = $this->yyVals[0+$yyTop];
        }

    function _110($yyTop)  					// line 1206 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->yyVal = $this->yyVals[-1+$yyTop];
            if (is_string($this->yyVal[$index])) {
                $this->yyVal[$index] .= str_replace(array('<<', '>>', '/'), array('<', '>', ''), $this->yyVals[0+$yyTop]);
            } else {
                $this->yyVal[] = str_replace(array('<<', '>>', '/'), array('<', '>', ''), $this->yyVals[0+$yyTop]);
            }
        }

    function _111($yyTop)  					// line 1215 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->yyVal = $this->yyVals[-1+$yyTop];
            $this->yyVal[] = $this->yyVals[0+$yyTop];
        }

    function _112($yyTop)  					// line 1220 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            if ($this->yyVals[0+$yyTop] == '{@}') {
                $t = array('{@');
            } elseif ($this->yyVals[0+$yyTop] == '{@*}') {
                $t = array('*/');
            } else {
                $t = array('');
            }
            $this->yyVal = $this->yyVals[-1+$yyTop];
            if (is_string($this->yyVal[$index])) {
                $this->yyVal[$index] .= $t;
            } else {
                $this->yyVal[] = $t;
            }
        }

    function _113($yyTop)  					// line 1236 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->yyVal = $this->yyVals[-1+$yyTop];
            $this->yyVal[] = $this->yyVals[0+$yyTop];
        }

    function _114($yyTop)  					// line 1241 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->yyVal = $this->yyVals[-1+$yyTop];
            $this->yyVal[] = $this->yyVals[0+$yyTop];
        }

    function _116($yyTop)  					// line 1250 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->yyVal = str_replace(array('<<', '>>', '/'), array('<', '>', ''), $this->yyVals[0+$yyTop]);
        }

    function _117($yyTop)  					// line 1254 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->yyVal = array($this->yyVals[0+$yyTop]);
        }

    function _118($yyTop)  					// line 1258 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            if ($this->yyVals[0+$yyTop] == '{@}') {
                $this->yyVal = array('{@');
            } elseif ($this->yyVals[0+$yyTop] == '{@*}') {
                $this->yyVal = array('*/');
            } else {
                $this->yyVal = array('');
            }
        }

    function _119($yyTop)  					// line 1268 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->yyVal = array($this->yyVals[0+$yyTop]);
        }

    function _120($yyTop)  					// line 1272 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->yyVal = array($this->yyVals[0+$yyTop]);
        }

    function _121($yyTop)  					// line 1276 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->yyVal = array($this->yyVals[0+$yyTop]);
        }

    function _122($yyTop)  					// line 1280 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->yyVal = $this->yyVals[-1+$yyTop];
            $this->yyVal[] = $this->yyVals[0+$yyTop];
        }

    function _123($yyTop)  					// line 1285 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->yyVal = $this->yyVals[-1+$yyTop];
            $index = count($this->yyVal) - 1;
            if (is_string($this->yyVal[$index])) {
                $this->yyVal[$index] .= str_replace(array('<<', '>>', '/'), array('<', '>', ''), $this->yyVals[0+$yyTop]);
            } else {
                $this->yyVal[] = str_replace(array('<<', '>>', '/'), array('<', '>', ''), $this->yyVals[0+$yyTop]);
            }
        }

    function _124($yyTop)  					// line 1295 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->yyVal = $this->yyVals[-1+$yyTop];
            $this->yyVal[] = $this->yyVals[0+$yyTop];
        }

    function _125($yyTop)  					// line 1300 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            if ($this->yyVals[0+$yyTop] == '{@}') {
                $t = array('{@');
            } elseif ($this->yyVals[0+$yyTop] == '{@*}') {
                $t = array('*/');
            } else {
                $t = array('');
            }
            $this->yyVal = $this->yyVals[-1+$yyTop];
            $index = count($this->yyVal) - 1;
            if (is_string($this->yyVal[$index])) {
                $this->yyVal[$index] .= $t;
            } else {
                $this->yyVal[] = $t;
            }
        }

    function _126($yyTop)  					// line 1317 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->yyVal = $this->yyVals[-1+$yyTop];
            $index = count($this->yyVal) - 1;
            if (is_string($this->yyVal[$index])) {
                $this->yyVal[$index] .= $this->yyVals[0+$yyTop];
            } else {
                $this->yyVal[] = $this->yyVals[0+$yyTop];
            }
        }

    function _127($yyTop)  					// line 1327 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->yyVal = $this->yyVals[-1+$yyTop];
            $this->yyVal[] = $this->yyVals[0+$yyTop];
        }

    function _128($yyTop)  					// line 1332 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->yyVal = $this->yyVals[-1+$yyTop];
            $this->yyVal[] = $this->yyVals[0+$yyTop];
        }

    function _129($yyTop)  					// line 1340 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->yyVal = array('list' => $this->yyVals[-1+$yyTop], 'type' => $this->yyVals[-2+$yyTop]);
        }

    function _130($yyTop)  					// line 1347 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->yyVal = array('list' => $this->yyVals[-1+$yyTop], 'type' => $this->yyVals[-2+$yyTop]);
        }

    function _131($yyTop)  					// line 1354 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->yyVal = $this->_parseInlineTag($this->yyVals[-2+$yyTop], $this->yyVals[-1+$yyTop]);
        }

    function _132($yyTop)  					// line 1358 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            $this->yyVal = $this->_parseInlineTag($this->yyVals[-1+$yyTop], array());
        }

    function _133($yyTop)  					// line 1365 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            if ($this->_options['parseInternal']) {
                $this->yyVal = $this->yyVals[-1+$yyTop];
            } else {
                $this->yyVal = '';
            }
        }

    function _134($yyTop)  					// line 1376 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"
    {
            if ($this->_options['parseInternal']) {
                $this->yyVal = $this->yyVals[-1+$yyTop];
            } else {
                $this->yyVal = '';
            }
        }
					// line 1590 "-"

					// line 1384 "C:/devel/PHP_Parser/Parser/DocBlock/Default.jay"

    /**#@-*/
}
					// line 1596 "-"

  $GLOBALS['_PHP_PARSER_DOCBLOCK_DEFAULT']['yyLhs']  = array(              -1,
    0,    0,    0,    0,    0,    0,    3,    2,    2,    2,
    5,    5,    5,    4,    4,    1,    1,    1,    1,    1,
    1,    1,    1,    1,    1,    1,    1,    1,    1,    1,
    1,    1,   11,   11,   11,   11,   11,   11,   11,   11,
   11,   11,   11,   11,   11,   11,   11,   11,    6,    6,
    6,    7,    7,    7,    7,    7,    7,    7,   12,   12,
   12,   12,   12,   12,   12,   15,   21,   16,   22,   17,
   23,   18,   24,   19,   25,   20,   26,   27,   27,   28,
   28,   29,   30,    8,    8,   13,   13,   31,   31,   32,
   32,   35,   35,   35,   33,   33,   37,   37,   37,   38,
   38,   38,   34,   34,   34,   34,   34,   34,   34,   34,
   34,   34,   34,   34,   36,   36,   36,   36,   36,   36,
   36,   36,   36,   36,   36,   36,   36,   36,   39,   40,
    9,    9,   10,   14,
  );
  $GLOBALS['_PHP_PARSER_DOCBLOCK_DEFAULT']['yyLen'] = array(           2,
    1,    2,    3,    2,    2,    1,    2,    1,    2,    3,
    0,    1,    3,    1,    2,    1,    1,    1,    1,    1,
    1,    1,    1,    2,    2,    2,    2,    2,    2,    2,
    2,    2,    1,    1,    1,    1,    1,    1,    1,    1,
    2,    2,    2,    2,    2,    2,    2,    2,    3,    4,
    2,    1,    1,    1,    1,    1,    1,    1,    1,    1,
    1,    1,    1,    1,    1,    3,    3,    3,    3,    3,
    3,    3,    3,    3,    3,    3,    3,    1,    2,    1,
    2,    3,    3,    1,    3,    1,    3,    3,    4,    3,
    4,    1,    1,    1,    1,    2,    1,    1,    1,    2,
    2,    2,    1,    1,    1,    1,    1,    1,    2,    2,
    2,    2,    2,    2,    1,    1,    1,    1,    1,    1,
    1,    2,    2,    2,    2,    2,    2,    2,    3,    3,
    4,    3,    3,    3,
  );
  $GLOBALS['_PHP_PARSER_DOCBLOCK_DEFAULT']['yyDefRed'] = array(            0,
   97,   98,   99,    0,    0,    0,    0,    0,    0,    0,
    0,   22,   52,   21,   16,   23,    0,    0,    0,    0,
    0,    6,    0,   14,   17,   18,   19,   20,   53,   54,
   55,   56,   57,   58,    0,    0,   95,   96,    0,    0,
    0,    0,    0,    0,   59,   38,   33,   39,    0,   40,
   36,    0,   34,   35,   37,   60,   61,   62,   63,   64,
   65,    0,    0,    0,    0,   78,    0,    0,    0,    0,
    0,    0,    0,    0,    0,   29,   30,   28,   24,   31,
    0,    2,    0,   25,   26,   27,   32,    5,   15,    0,
    0,  103,    0,  104,  106,  107,  105,    0,    0,  108,
    0,    0,   80,    0,    0,    0,    0,    0,    0,    0,
   46,   41,   47,   48,   44,   42,   43,   45,    0,    0,
  115,  116,  118,  119,  117,  120,    0,    0,  121,    0,
   76,   79,   68,    0,   66,   72,   74,   70,  133,    0,
  132,    0,    0,    3,   85,    0,  100,  101,  102,  109,
   92,   93,  110,  112,   94,  113,  111,   88,  114,    0,
    0,   77,   81,   69,   67,   73,   75,   71,  134,   50,
   87,    0,  122,  123,  125,  126,  124,  127,   90,  128,
    0,   82,    0,  131,   89,  129,   83,   91,  130,
  );
  $GLOBALS['_PHP_PARSER_DOCBLOCK_DEFAULT']['yyDgoto']  = array(            20,
   67,   22,   83,   23,   68,   24,   25,   26,   27,   28,
   52,   53,   54,   55,   29,   30,   31,   32,   33,   34,
   56,   57,   58,   59,   60,   61,   65,  102,   66,  103,
   35,   62,   36,   98,  158,  127,   37,   99,  100,  129,
  );
  $GLOBALS['_PHP_PARSER_DOCBLOCK_DEFAULT']['yySindex'] = array(          349,
    0,    0,    0,  126,  678,   12,  715,  715,  715,  715,
  715,    0,    0,    0,    0,    0,  715,   -2,   66,    0,
  312,    0,   34,    0,    0,    0,    0,    0,    0,    0,
    0,    0,    0,    0,  183,  794,    0,    0,   28,  678,
  678,  678,  678,  678,    0,    0,    0,    0,  102,    0,
    0,  386,    0,    0,    0,    0,    0,    0,    0,    0,
    0,  215,  764,  715,   95,    0,  423,   10,   69,   -5,
  116,   43,  153,  715,  128,    0,    0,    0,    0,    0,
  715,    0,  190,    0,    0,    0,    0,    0,    0,  222,
  794,    0,  144,    0,    0,    0,    0,   30,  794,    0,
  678,  152,    0,  456,  493,  530,  567,  604,   73,  118,
    0,    0,    0,    0,    0,    0,    0,    0,  232,  764,
    0,    0,    0,    0,    0,    0,   -4,  764,    0,    6,
    0,    0,    0,  715,    0,    0,    0,    0,    0,  127,
    0,  151,  127,    0,    0,   30,    0,    0,    0,    0,
    0,    0,    0,    0,    0,    0,    0,    0,    0,   30,
  641,    0,    0,    0,    0,    0,    0,    0,    0,    0,
    0,   -4,    0,    0,    0,    0,    0,    0,    0,    0,
   -4,    0,  423,    0,    0,    0,    0,    0,    0,
  );
  $GLOBALS['_PHP_PARSER_DOCBLOCK_DEFAULT']['yyRindex'] = array(            0,
    0,    0,    0,    0,    0,    0,   63,   89,  138,  123,
  174,    0,    0,    0,    0,    0,  165,  219,    0,    0,
  227,    0,    0,    0,    0,    0,    0,    0,    0,    0,
    0,    0,    0,    0,  275,    0,    0,    0,    0,    0,
    0,    0,    0,    0,    0,    0,    0,    0,    0,    0,
    0,   60,    0,    0,    0,    0,    0,    0,    0,    0,
    0,  818,    0,   29,    0,    0,  231,    0,    0,    0,
    0,    0,    0,   19,    0,    0,    0,    0,    0,    0,
   22,    0,  229,    0,    0,    0,    0,    0,    0,    0,
    0,    0,    0,    0,    0,    0,    0,    0,    0,    0,
    0,    0,    0,    0,    0,    0,    0,    0,    0,   74,
    0,    0,    0,    0,    0,    0,    0,    0,    0,    0,
    0,    0,    0,    0,    0,    0,    0,    0,    0,    0,
    0,    0,    0,    0,    0,    0,    0,    0,    0,  236,
    0,    0,   61,    0,    0,    0,    0,    0,    0,    0,
    0,    0,    0,    0,    0,    0,    0,    0,    0,    0,
    0,    0,    0,    0,    0,    0,    0,    0,    0,    0,
    0,    0,    0,    0,    0,    0,    0,    0,    0,    0,
    0,    0,  735,    0,    0,    0,    0,    0,    0,
  );
  $GLOBALS['_PHP_PARSER_DOCBLOCK_DEFAULT']['yyGindex'] = array(            0,
    7,   -3,    0,  188,  197,   -7,  -12,  -18,   32,  -16,
   48,   49,   14,   90,    0,    0,    0,    0,    0,    0,
    0,    0,    0,    0,    0,    0,    0,    0,  167,  137,
    0,    0,  139,  112,  -46,   92,   13,  -11,  -83, -116,
  );
  $GLOBALS['_PHP_PARSER_DOCBLOCK_DEFAULT']['yyTable'] = array(           173,
  151,  152,   85,   93,   87,   39,   21,   40,   84,   41,
  180,   42,   43,   44,  159,   89,   38,   82,    9,   88,
  136,   11,   64,   96,   45,  174,  182,  175,   74,  134,
  176,  133,   19,  150,  151,  152,   51,   93,  101,    6,
  134,    7,    5,    8,  134,    9,   10,   11,   85,   11,
   87,  128,   86,   11,   84,  180,   11,   11,   13,  153,
    7,  154,  159,   11,  180,  117,   19,   97,   51,   18,
  138,   51,   51,   51,   51,   51,  159,  134,   96,  144,
  179,    5,   49,  115,   11,  156,   96,  104,  105,  106,
  107,  108,  135,   51,  125,   51,    7,   11,   86,  185,
  116,   89,   38,  134,   75,   64,  169,   49,  128,   49,
    5,  126,   11,  186,  131,  128,  128,  117,  117,  117,
  117,  117,   97,   11,  170,  188,    1,    2,    3,  157,
   97,   38,   51,  156,  189,  115,  115,  115,  115,  115,
  183,  118,  137,   63,  147,  148,  149,  156,  161,   11,
  134,  125,  116,  116,  116,  116,  116,   11,  177,  125,
  128,  134,  101,   11,   85,  141,   87,  142,  126,  128,
   84,  162,   11,   91,  117,  178,  126,  157,   63,   63,
   63,   63,   63,    1,    2,    3,  139,  134,  184,   90,
   63,  157,  115,  118,  118,  118,  118,  118,   11,   11,
  120,   11,  146,  177,   69,   70,   71,   72,   11,  116,
  160,  172,  177,   73,   86,    1,    2,    3,    8,  181,
  178,  119,    1,    2,    3,   18,    1,  145,    4,  178,
   12,  132,    1,    2,    3,   10,  109,  171,  163,   63,
    0,    0,   63,   63,   63,   63,   63,    0,    0,    0,
  118,   12,   12,  155,   12,    0,   12,   12,   12,    0,
  130,    0,    0,    0,   12,   12,   12,    0,    0,    0,
  140,    0,    0,    0,   84,    0,    0,  143,    0,    0,
    0,    0,    0,   84,   84,    0,   84,  155,   84,    0,
   84,   84,   84,   84,    0,   84,   84,    0,   84,   63,
   84,   84,   84,   84,   84,   84,   84,   84,   84,   84,
   84,   84,    1,    2,    3,    0,    0,    0,    4,    0,
   76,    6,    0,    7,    0,    8,    0,    9,   10,   11,
   77,    0,    0,    0,    0,    0,    0,    0,    0,    0,
   13,   78,   79,   80,   17,    0,   81,   18,   19,    1,
    2,    3,    0,    0,    0,    4,    0,    5,    6,    0,
    7,    0,    8,    0,    9,   10,   11,   12,    0,    0,
    0,    0,    0,    0,    0,    0,    0,   13,   14,   15,
   16,   17,    0,    0,   18,   19,    1,    2,    3,    0,
    0,    0,    4,    0,    0,   39,    0,   40,    0,   41,
    0,   42,   43,   44,  110,    0,    0,    0,    0,    0,
    0,    0,    0,    0,   45,  111,  112,  113,   49,    0,
  114,    0,   19,    1,    2,    3,    0,    0,    0,    4,
    0,   76,    6,    0,    7,    0,    8,    0,    9,   10,
   11,   77,    0,    0,    0,    0,    0,    0,    0,    0,
    0,   13,   78,   79,   80,   17,    1,    2,    3,   19,
    0,    0,    4,    0,    0,   39,    0,   40,    0,   41,
    0,   42,   43,   44,    0,    0,    0,  164,    0,    0,
    0,    0,    0,    0,   45,  111,  112,  113,   49,    0,
  114,    0,   19,    1,    2,    3,    0,    0,    0,    4,
    0,    0,   39,    0,   40,    0,   41,    0,   42,   43,
   44,    0,    0,    0,    0,    0,  165,    0,    0,    0,
    0,   45,  111,  112,  113,   49,    0,  114,    0,   19,
    1,    2,    3,    0,    0,    0,    4,    0,    0,   39,
    0,   40,    0,   41,    0,   42,   43,   44,    0,    0,
    0,    0,    0,    0,    0,  166,    0,    0,   45,  111,
  112,  113,   49,    0,  114,    0,   19,    1,    2,    3,
    0,    0,    0,    4,    0,    0,   39,    0,   40,    0,
   41,    0,   42,   43,   44,    0,    0,    0,    0,    0,
    0,    0,    0,  167,    0,   45,  111,  112,  113,   49,
    0,  114,    0,   19,    1,    2,    3,    0,    0,    0,
    4,    0,    0,   39,    0,   40,    0,   41,    0,   42,
   43,   44,    0,    0,    0,    0,    0,    0,    0,    0,
    0,  168,   45,  111,  112,  113,   49,    0,  114,    0,
   19,    1,    2,    3,    0,    0,    0,    4,    0,    0,
   39,    0,   40,    0,   41,    0,   42,   43,   44,    0,
    0,  187,    0,    0,    0,    0,    0,    0,    0,   45,
  111,  112,  113,   49,    0,  114,    0,   19,    1,    2,
    3,    0,    0,    0,    4,    0,    0,   39,    0,   40,
    0,   41,    0,   42,   43,   44,    0,    0,    0,    0,
    0,    0,    0,    0,    0,    0,   45,   46,   47,   48,
   49,    0,   50,    0,   19,    1,    2,    3,    0,    0,
    0,    4,    0,    0,    6,    0,    7,    0,    8,    0,
    9,   10,   11,   12,   13,    0,    0,    0,    0,    0,
    0,    0,    0,   13,   14,   15,   16,   17,    0,    0,
    0,   19,    0,    0,    0,   13,   13,    0,   13,    0,
   13,   13,   13,    0,    0,    0,    0,  121,   13,   13,
   13,   93,    0,   39,    0,   40,    0,   41,    0,   42,
   43,   44,    0,    0,    0,    0,    0,    0,    0,    0,
    0,    0,   45,  122,    0,  123,    0,   92,  124,    0,
   19,   93,    0,    6,    0,    7,    0,    8,    0,    9,
   10,   11,    0,    0,    0,    0,    0,    0,    0,    0,
    0,    0,   13,   94,    0,   95,   86,   86,    0,   86,
   19,   86,    0,   86,   86,   86,   86,    0,   86,   86,
    0,   86,    0,   86,   86,   86,   86,   86,   86,   86,
   86,   86,   86,   86,   86,
  );
 $GLOBALS['_PHP_PARSER_DOCBLOCK_DEFAULT']['yyCheck'] = array(             4,
    5,    6,   21,    8,   21,   10,    0,   12,   21,   14,
  127,   16,   17,   18,   98,   23,    4,   21,    0,   23,
   26,    0,   11,   36,   29,   30,   21,   32,   31,   35,
   35,   22,   37,    4,    5,    6,    5,    8,   11,   10,
   35,   12,    9,   14,   35,   16,   17,   18,   67,   21,
   67,   63,   21,   35,   67,  172,   35,   36,   29,   30,
    0,   32,  146,   35,  181,   52,   37,   36,    9,   36,
   28,   40,   41,   42,   43,   44,  160,   35,   91,   83,
  127,    9,    9,   52,   22,   98,   99,   40,   41,   42,
   43,   44,   24,   34,   63,   36,   36,   35,   67,  146,
   52,  109,   90,   35,   39,   11,   34,   34,  120,   36,
    9,   63,   24,  160,   20,  127,  128,  104,  105,  106,
  107,  108,   91,   35,    7,  172,    1,    2,    3,   98,
   99,  119,  101,  146,  181,  104,  105,  106,  107,  108,
  134,   52,   27,    5,    1,    2,    3,  160,  101,   27,
   35,  120,  104,  105,  106,  107,  108,   35,  127,  128,
  172,   35,   11,   26,  183,   38,  183,   40,  120,  181,
  183,   20,   35,   35,  161,  127,  128,  146,   40,   41,
   42,   43,   44,    1,    2,    3,   34,   35,   38,    7,
   52,  160,  161,  104,  105,  106,  107,  108,   34,   35,
   62,   28,   91,  172,    8,    9,   10,   11,   35,  161,
   99,  120,  181,   17,  183,    1,    2,    3,    0,  128,
  172,    7,    1,    2,    3,   36,    0,    6,    0,  181,
    0,   65,    1,    2,    3,    0,   49,    6,  102,  101,
   -1,   -1,  104,  105,  106,  107,  108,   -1,   -1,   -1,
  161,   21,   22,  258,   24,   -1,   26,   27,   28,   -1,
   64,   -1,   -1,   -1,   34,   35,   36,   -1,   -1,   -1,
   74,   -1,   -1,   -1,    0,   -1,   -1,   81,   -1,   -1,
   -1,   -1,   -1,    9,   10,   -1,   12,  258,   14,   -1,
   16,   17,   18,   19,   -1,   21,   22,   -1,   24,  161,
   26,   27,   28,   29,   30,   31,   32,   33,   34,   35,
   36,   37,    1,    2,    3,   -1,   -1,   -1,    7,   -1,
    9,   10,   -1,   12,   -1,   14,   -1,   16,   17,   18,
   19,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,
   29,   30,   31,   32,   33,   -1,   35,   36,   37,    1,
    2,    3,   -1,   -1,   -1,    7,   -1,    9,   10,   -1,
   12,   -1,   14,   -1,   16,   17,   18,   19,   -1,   -1,
   -1,   -1,   -1,   -1,   -1,   -1,   -1,   29,   30,   31,
   32,   33,   -1,   -1,   36,   37,    1,    2,    3,   -1,
   -1,   -1,    7,   -1,   -1,   10,   -1,   12,   -1,   14,
   -1,   16,   17,   18,   19,   -1,   -1,   -1,   -1,   -1,
   -1,   -1,   -1,   -1,   29,   30,   31,   32,   33,   -1,
   35,   -1,   37,    1,    2,    3,   -1,   -1,   -1,    7,
   -1,    9,   10,   -1,   12,   -1,   14,   -1,   16,   17,
   18,   19,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,
   -1,   29,   30,   31,   32,   33,    1,    2,    3,   37,
   -1,   -1,    7,   -1,   -1,   10,   -1,   12,   -1,   14,
   -1,   16,   17,   18,   -1,   -1,   -1,   22,   -1,   -1,
   -1,   -1,   -1,   -1,   29,   30,   31,   32,   33,   -1,
   35,   -1,   37,    1,    2,    3,   -1,   -1,   -1,    7,
   -1,   -1,   10,   -1,   12,   -1,   14,   -1,   16,   17,
   18,   -1,   -1,   -1,   -1,   -1,   24,   -1,   -1,   -1,
   -1,   29,   30,   31,   32,   33,   -1,   35,   -1,   37,
    1,    2,    3,   -1,   -1,   -1,    7,   -1,   -1,   10,
   -1,   12,   -1,   14,   -1,   16,   17,   18,   -1,   -1,
   -1,   -1,   -1,   -1,   -1,   26,   -1,   -1,   29,   30,
   31,   32,   33,   -1,   35,   -1,   37,    1,    2,    3,
   -1,   -1,   -1,    7,   -1,   -1,   10,   -1,   12,   -1,
   14,   -1,   16,   17,   18,   -1,   -1,   -1,   -1,   -1,
   -1,   -1,   -1,   27,   -1,   29,   30,   31,   32,   33,
   -1,   35,   -1,   37,    1,    2,    3,   -1,   -1,   -1,
    7,   -1,   -1,   10,   -1,   12,   -1,   14,   -1,   16,
   17,   18,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,
   -1,   28,   29,   30,   31,   32,   33,   -1,   35,   -1,
   37,    1,    2,    3,   -1,   -1,   -1,    7,   -1,   -1,
   10,   -1,   12,   -1,   14,   -1,   16,   17,   18,   -1,
   -1,   21,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   29,
   30,   31,   32,   33,   -1,   35,   -1,   37,    1,    2,
    3,   -1,   -1,   -1,    7,   -1,   -1,   10,   -1,   12,
   -1,   14,   -1,   16,   17,   18,   -1,   -1,   -1,   -1,
   -1,   -1,   -1,   -1,   -1,   -1,   29,   30,   31,   32,
   33,   -1,   35,   -1,   37,    1,    2,    3,   -1,   -1,
   -1,    7,   -1,   -1,   10,   -1,   12,   -1,   14,   -1,
   16,   17,   18,   19,    0,   -1,   -1,   -1,   -1,   -1,
   -1,   -1,   -1,   29,   30,   31,   32,   33,   -1,   -1,
   -1,   37,   -1,   -1,   -1,   21,   22,   -1,   24,   -1,
   26,   27,   28,   -1,   -1,   -1,   -1,    4,   34,   35,
   36,    8,   -1,   10,   -1,   12,   -1,   14,   -1,   16,
   17,   18,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,
   -1,   -1,   29,   30,   -1,   32,   -1,    4,   35,   -1,
   37,    8,   -1,   10,   -1,   12,   -1,   14,   -1,   16,
   17,   18,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,
   -1,   -1,   29,   30,   -1,   32,    9,   10,   -1,   12,
   37,   14,   -1,   16,   17,   18,   19,   -1,   21,   22,
   -1,   24,   -1,   26,   27,   28,   29,   30,   31,   32,
   33,   34,   35,   36,   37,
  );

  $GLOBALS['_PHP_PARSER_DOCBLOCK_DEFAULT']['yyFinal'] = 20;
$GLOBALS['_PHP_PARSER_DOCBLOCK_DEFAULT']['yyRule'] = array(
   "\$accept :  docblock ",
    "docblock :  paragraph ",
    "docblock :  paragraph   tags ",
    "docblock :  paragraph   text   tags ",
    "docblock :  paragraph   text ",
    "docblock :  paragraphs_with_p   tags ",
    "docblock :  tags ",
    "text :  T_DOUBLE_NL   paragraphs ",
    "tags :  T_TAG ",
    "tags :  T_TAG   T_TEXT ",
    "tags :  T_TAG   T_TEXT   paragraphs ",
    "paragraphs :",
    "paragraphs :  paragraph ",
    "paragraphs :  paragraphs   T_DOUBLE_NL   paragraph ",
    "paragraphs_with_p :  paragraph_with_p ",
    "paragraphs_with_p :  paragraphs_with_p   paragraph_with_p ",
    "paragraph :  T_TEXT ",
    "paragraph :  htmltag ",
    "paragraph :  simplelist ",
    "paragraph :  inlinetag ",
    "paragraph :  internaltag ",
    "paragraph :  T_ESCAPED_TAG ",
    "paragraph :  T_CLOSE_P ",
    "paragraph :  T_INLINE_ESC ",
    "paragraph :  paragraph   T_TEXT ",
    "paragraph :  paragraph   htmltag ",
    "paragraph :  paragraph   simplelist ",
    "paragraph :  paragraph   inlinetag ",
    "paragraph :  paragraph   T_ESCAPED_TAG ",
    "paragraph :  paragraph   T_OPEN_P ",
    "paragraph :  paragraph   T_CLOSE_P ",
    "paragraph :  paragraph   T_INLINE_ESC ",
    "paragraph :  paragraph   internaltag ",
    "text_expr_with_p :  T_TEXT ",
    "text_expr_with_p :  htmltag_with_p ",
    "text_expr_with_p :  simplelist_with_p ",
    "text_expr_with_p :  inlinetag ",
    "text_expr_with_p :  internaltag_with_p ",
    "text_expr_with_p :  T_ESCAPED_TAG ",
    "text_expr_with_p :  T_INLINE_ESC ",
    "text_expr_with_p :  T_DOUBLE_NL ",
    "text_expr_with_p :  text_expr_with_p   T_TEXT ",
    "text_expr_with_p :  text_expr_with_p   htmltag_with_p ",
    "text_expr_with_p :  text_expr_with_p   simplelist_with_p ",
    "text_expr_with_p :  text_expr_with_p   inlinetag ",
    "text_expr_with_p :  text_expr_with_p   internaltag_with_p ",
    "text_expr_with_p :  text_expr_with_p   T_ESCAPED_TAG ",
    "text_expr_with_p :  text_expr_with_p   T_INLINE_ESC ",
    "text_expr_with_p :  text_expr_with_p   T_DOUBLE_NL ",
    "paragraph_with_p :  T_OPEN_P   text_expr_with_p   T_CLOSE_P ",
    "paragraph_with_p :  T_OPEN_P   text_expr_with_p   T_CLOSE_P   T_WHITESPACE ",
    "paragraph_with_p :  T_OPEN_P   text_expr_with_p ",
    "htmltag :  T_XML_TAG ",
    "htmltag :  btag ",
    "htmltag :  codetag ",
    "htmltag :  samptag ",
    "htmltag :  kbdtag ",
    "htmltag :  vartag ",
    "htmltag :  htmllist ",
    "htmltag_with_p :  T_XML_TAG ",
    "htmltag_with_p :  btag_with_p ",
    "htmltag_with_p :  codetag_with_p ",
    "htmltag_with_p :  samptag_with_p ",
    "htmltag_with_p :  kbdtag_with_p ",
    "htmltag_with_p :  vartag_with_p ",
    "htmltag_with_p :  htmllist_with_p ",
    "btag :  T_OPEN_B   paragraphs   T_CLOSE_B ",
    "btag_with_p :  T_OPEN_B   text_expr_with_p   T_CLOSE_B ",
    "codetag :  T_OPEN_CODE   paragraphs   T_CLOSE_CODE ",
    "codetag_with_p :  T_OPEN_CODE   text_expr_with_p   T_CLOSE_CODE ",
    "samptag :  T_OPEN_SAMP   paragraphs   T_CLOSE_SAMP ",
    "samptag_with_p :  T_OPEN_SAMP   text_expr_with_p   T_CLOSE_SAMP ",
    "kbdtag :  T_OPEN_KBD   paragraphs   T_CLOSE_KBD ",
    "kbdtag_with_p :  T_OPEN_KBD   text_expr_with_p   T_CLOSE_KBD ",
    "vartag :  T_OPEN_VAR   paragraphs   T_CLOSE_VAR ",
    "vartag_with_p :  T_OPEN_VAR   text_expr_with_p   T_CLOSE_VAR ",
    "htmllist :  T_OPEN_LIST   listitems   T_CLOSE_LIST ",
    "htmllist_with_p :  T_OPEN_LIST   listitems_with_p   T_CLOSE_LIST ",
    "listitems :  listitem ",
    "listitems :  listitems   listitem ",
    "listitems_with_p :  listitem_with_p ",
    "listitems_with_p :  listitems_with_p   listitem_with_p ",
    "listitem :  T_OPEN_LI   paragraphs   T_CLOSE_LI ",
    "listitem_with_p :  T_OPEN_LI   text_expr_with_p   T_CLOSE_LI ",
    "simplelist :  simplelist_items ",
    "simplelist :  simplelist_items   T_WHITESPACE   T_SIMPLELIST_END ",
    "simplelist_with_p :  simplelist_items_with_p ",
    "simplelist_with_p :  simplelist_items_with_p   T_WHITESPACE   T_SIMPLELIST_END ",
    "simplelist_items :  bullet   simplelist_contents   simplelistend ",
    "simplelist_items :  simplelist_items   bullet   simplelist_contents   simplelistend ",
    "simplelist_items_with_p :  bullet   simplelist_contents_with_p   simplelistend ",
    "simplelist_items_with_p :  simplelist_items_with_p   bullet   simplelist_contents_with_p   simplelistend ",
    "simplelistend :  T_SIMPLELIST_NL ",
    "simplelistend :  T_SIMPLELIST_END ",
    "simplelistend :  EOF ",
    "bullet :  bullet_no_whitespace ",
    "bullet :  T_WHITESPACE   bullet_no_whitespace ",
    "bullet_no_whitespace :  T_BULLET ",
    "bullet_no_whitespace :  T_NBULLET ",
    "bullet_no_whitespace :  T_NDBULLET ",
    "nested_bullet :  T_NESTED_WHITESPACE   T_BULLET ",
    "nested_bullet :  T_NESTED_WHITESPACE   T_NBULLET ",
    "nested_bullet :  T_NESTED_WHITESPACE   T_NDBULLET ",
    "simplelist_contents :  T_SIMPLELIST ",
    "simplelist_contents :  T_ESCAPED_TAG ",
    "simplelist_contents :  inlinetag ",
    "simplelist_contents :  T_INLINE_ESC ",
    "simplelist_contents :  htmltag ",
    "simplelist_contents :  nested_simplelist ",
    "simplelist_contents :  simplelist_contents   T_SIMPLELIST ",
    "simplelist_contents :  simplelist_contents   T_ESCAPED_TAG ",
    "simplelist_contents :  simplelist_contents   inlinetag ",
    "simplelist_contents :  simplelist_contents   T_INLINE_ESC ",
    "simplelist_contents :  simplelist_contents   htmltag ",
    "simplelist_contents :  simplelist_contents   nested_simplelist ",
    "simplelist_contents_with_p :  T_SIMPLELIST ",
    "simplelist_contents_with_p :  T_ESCAPED_TAG ",
    "simplelist_contents_with_p :  inlinetag ",
    "simplelist_contents_with_p :  T_INLINE_ESC ",
    "simplelist_contents_with_p :  T_DOUBLE_NL ",
    "simplelist_contents_with_p :  htmltag_with_p ",
    "simplelist_contents_with_p :  nested_simplelist_with_p ",
    "simplelist_contents_with_p :  simplelist_contents_with_p   T_SIMPLELIST ",
    "simplelist_contents_with_p :  simplelist_contents_with_p   T_ESCAPED_TAG ",
    "simplelist_contents_with_p :  simplelist_contents_with_p   inlinetag ",
    "simplelist_contents_with_p :  simplelist_contents_with_p   T_INLINE_ESC ",
    "simplelist_contents_with_p :  simplelist_contents_with_p   T_DOUBLE_NL ",
    "simplelist_contents_with_p :  simplelist_contents_with_p   htmltag_with_p ",
    "simplelist_contents_with_p :  simplelist_contents_with_p   nested_simplelist_with_p ",
    "nested_simplelist :  nested_bullet   simplelist_contents   simplelistend ",
    "nested_simplelist_with_p :  nested_bullet   simplelist_contents_with_p   simplelistend ",
    "inlinetag :  T_INLINE_TAG_OPEN   T_INLINE_TAG_NAME   T_INLINE_TAG_CONTENTS   T_INLINE_TAG_CLOSE ",
    "inlinetag :  T_INLINE_TAG_OPEN   T_INLINE_TAG_NAME   T_INLINE_TAG_CLOSE ",
    "internaltag :  T_INTERNAL   paragraphs   T_ENDINTERNAL ",
    "internaltag_with_p :  T_INTERNAL   paragraphs_with_p   T_ENDINTERNAL ",
  );
  $GLOBALS['_PHP_PARSER_DOCBLOCK_DEFAULT']['yyName'] =array(    
    "end-of-file","T_BULLET","T_NBULLET","T_NDBULLET","T_SIMPLELIST",
    "T_SIMPLELIST_NL","T_SIMPLELIST_END","T_WHITESPACE",
    "T_NESTED_WHITESPACE","T_OPEN_P","T_OPEN_LIST","T_OPEN_LI",
    "T_OPEN_CODE","T_OPEN_PRE","T_OPEN_B","T_OPEN_I","T_OPEN_KBD",
    "T_OPEN_VAR","T_OPEN_SAMP","T_CLOSE_P","T_CLOSE_LIST","T_CLOSE_LI",
    "T_CLOSE_CODE","T_CLOSE_PRE","T_CLOSE_B","T_CLOSE_I","T_CLOSE_KBD",
    "T_CLOSE_VAR","T_CLOSE_SAMP","T_XML_TAG","T_ESCAPED_TAG","T_TEXT",
    "T_INLINE_ESC","T_INTERNAL","T_ENDINTERNAL","T_DOUBLE_NL","T_TAG",
    "T_INLINE_TAG_OPEN","T_INLINE_TAG_CLOSE","T_INLINE_TAG_NAME",
    "T_INLINE_TAG_CONTENTS",null,null,null,null,null,null,null,null,null,
    null,null,null,null,null,null,null,null,null,null,null,null,null,null,
    null,null,null,null,null,null,null,null,null,null,null,null,null,null,
    null,null,null,null,null,null,null,null,null,null,null,null,null,null,
    null,null,null,null,null,null,null,null,null,null,null,null,null,null,
    null,null,null,null,null,null,null,null,null,null,null,null,null,null,
    null,null,null,null,null,null,null,null,null,null,null,null,null,null,
    null,null,null,null,null,null,null,null,null,null,null,null,null,null,
    null,null,null,null,null,null,null,null,null,null,null,null,null,null,
    null,null,null,null,null,null,null,null,null,null,null,null,null,null,
    null,null,null,null,null,null,null,null,null,null,null,null,null,null,
    null,null,null,null,null,null,null,null,null,null,null,null,null,null,
    null,null,null,null,null,null,null,null,null,null,null,null,null,null,
    null,null,null,null,null,null,null,null,null,null,null,null,null,null,
    null,null,null,null,null,null,null,null,null,null,null,null,null,null,
    null,null,null,null,null,null,null,null,null,null,null,null,"EOF",
  );
 ?>
