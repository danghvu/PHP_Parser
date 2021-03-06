<?php
/**
 * A simple example
 * @package PEAR_PackageFileManager
 */
/**
 * Include the package file manager
 */
require_once('PEAR/PackageFileManager.php');
$test = new PEAR_PackageFileManager;
if (PEAR::isError($test)) {
    echo $test->getMessage();
    exit;
}
$test->setOptions(
array('baseinstalldir' => 'PHP',
'version' => '0.2',
'packagedirectory' => 'C:/devel/PHP_Parser',
'state' => 'devel',
'filelistgenerator' => 'cvs',
'dirroles' => array('Parser/tests' => 'test'),
'exceptions' => array('generatePackage.xml.php' => 'test'), // so I can run it here easily
'simpleoutput' => true,
'notes' => 'Second development release.  Not even alpha yet.

Re-organized into two parsers, Structure and Extendable.  Structure is the old parser, Extendable
is designed to be overridden and provides access to every token.
Added DocBlock parsing in from phpDocumentor 2.0 development',
'ignore' => array('y.output')
));
$e = $test->addDependency('PHP', '4.3', 'ge', 'php');
if (PEAR::isError($e)) {
    echo $e->getMessage();
}
if (!isset($_GET['make'])) {
    $e = $test->debugPackageFile();
} else {
    $e = $test->writePackageFile();
}
if (PEAR::isError($e)) {
    echo $e->getMessage();
}
?><a href="<?php echo $_SERVER['PHP_SELF']; ?>?make=1">Make file</a>