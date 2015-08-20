<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "piwikintegration".
 *
 * Auto generated 11-03-2013 20:23
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array (
	'title' => 'Piwik Backend integration',
	'description' => 'Uses EXT:piwik to inserts Data in the HTML header and gives BE-Users the right to see the data for their sites. Autoupdate of Piwik will work as TYPO3-Admin!',
	'category' => 'module',
	'version' => '4.3.3',
	'module' => 'mod1',
	'state' => 'alpha',
	'uploadfolder' => 0,
	'createDirs' => '',
	'clearcacheonload' => 0,
	'author' => 'Kay Strobach',
	'author_email' => 'kay.strobach@typo3.org',
	'author_company' => '',
	'constraints' => 
	array (
		'depends' => 
		array (
			'php' => '5.2.0-6.0.0',
			'typo3' => '6.2.5-6.2.99',
		),
		'conflicts' => 
		array (
			'dbal' => '1.0.0-99.0.0',
		),
		'suggests' => 
		array (
			'piwik' => '2.0.0-2.999.0',
		),
	),
);

?>
