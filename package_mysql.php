<?php

require_once 'PEAR/PackageFileManager2.php';
PEAR::setErrorHandling(PEAR_ERROR_DIE);

$version = '1.5.0a1';
$state = 'alpha';
$notes = <<<EOT
- fixed bug #10024: Added new option 'lob_allow_url_include' (default false) to
  [dis]allow inserting a LOB from an url (file, http, ...).
- fixed bug #10986: Using more random statement names (request #11625)
- fixed bug #11055: Using placeholders with := variable assignment fails [bekarau]
- initial support for FOREIGN KEY constraints in the Manager and Reverse modules
- request #11389: added many new MySQL 5.1 error codes in errorInfo()
- fixed bug #11428: propagate quote() errors with invalid data types
- fixed bug #11590: _getServerCapabilities() has to be called once per connection
- fixed bug #11790: avoid array_diff() because it has a memory leak in PHP 5.1.x
- fixed some E_STRICT errors with PHP5
- fixed bug #12010: MDB2_PORTABILITY_RTRIM option was ignored
- fixed bug #12083: createTable() in the Manager module now returns MDB2_OK on success,
  as documented
- fixed bug #12217: mysql_num_rows() returns FALSE on failure, not NULL (thanks to zaa@zaa.pp.ru)
- fixed bug #12242: missing charset info in the Reverse module (patch by Carsten Wiedmann)
- fixed bug #12269: tableInfo() in the Reverse module detect 'clob' data type
  as first option
- fixed bug #12336: supply default value for NOT NULL timestamp fields

note:
- the multi_query test failes because this is not supported by ext/mysql

open todo items:
- use a trigger to emulate setting default now()
EOT;

$description = 'This is the MySQL MDB2 driver.';
$packagefile = './package_mysql.xml';

$options = array(
    'filelistgenerator' => 'cvs',
    'changelogoldtonew' => false,
    'simpleoutput'      => true,
    'baseinstalldir'    => '/',
    'packagedirectory'  => './',
    'packagefile'       => $packagefile,
    'clearcontents'     => false,
    'include'           => array('*mysql*'),
    'ignore'            => array('package_mysql.php', '*mysqli*'),
);

$package = &PEAR_PackageFileManager2::importOptions($packagefile, $options);
$package->setPackageType('php');

$package->clearDeps();
$package->setPhpDep('4.3.0');
$package->setPearInstallerDep('1.4.0b1');
$package->addPackageDepWithChannel('required', 'MDB2', 'pear.php.net', '2.5.0a1');
$package->addExtensionDep('required', 'mysql');

$package->addRelease();
$package->generateContents();
$package->setReleaseVersion($version);
$package->setAPIVersion($version);
$package->setReleaseStability($state);
$package->setAPIStability($state);
$package->setNotes($notes);
$package->setDescription($description);
$package->addGlobalReplacement('package-info', '@package_version@', 'version');

if (isset($_GET['make']) || (isset($_SERVER['argv']) && @$_SERVER['argv'][1] == 'make')) {
    $package->writePackageFile();
} else {
    $package->debugPackageFile();
}