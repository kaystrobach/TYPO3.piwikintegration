<?php

/**
 * Add widgets
 */ 

 
Piwik_AddWidget('TYPO3 Widgets', 'TYPO3 All News',                'TYPO3Widgets', 'rssAllNews');
Piwik_AddWidget('TYPO3 Widgets', 'TYPO3 All Team News',           'TYPO3Widgets', 'rssAllTeamNews');
Piwik_AddWidget('TYPO3 Widgets', 'TYPO3 Community',               'TYPO3Widgets', 'rssCommunity');
Piwik_AddWidget('TYPO3 Widgets', 'TYPO3 Content Rendering Group', 'TYPO3Widgets', 'rssContentRenderingGroup');
Piwik_AddWidget('TYPO3 Widgets', 'TYPO3 Development',             'TYPO3Widgets', 'rssDevelopment');
Piwik_AddWidget('TYPO3 Widgets', 'TYPO3 Extensions',              'TYPO3Widgets', 'rssExtensions');
Piwik_AddWidget('TYPO3 Widgets', 'TYPO3 Security',                'TYPO3Widgets', 'rssSecurity');
Piwik_AddWidget('TYPO3 Widgets', 'TYPO3.Org',                     'TYPO3Widgets', 'rssTypo3Org');
Piwik_AddWidget('TYPO3 Widgets', 'TYPO3 Associaton',              'TYPO3Widgets', 'rssTypo3Associaton');

class Piwik_TYPO3Widgets  extends Piwik_Plugin {
	/**
	 * get extension information
	 *
	 * @return	array		with information
	 */
	public function getInformation()
	{
		include(PIWIK_INCLUDE_PATH.'/piwikintegration.php');
		return array(
			'name' => 'TYPO3Widgets',
			'description' => 'Widgets to show TYPO3 specific data.',
			'author' => 'Kay Strobach',
			'homepage' => 'http://kay-strobach.de/',
		    'version' => $piwikPatchVersion,
			);
	}
}