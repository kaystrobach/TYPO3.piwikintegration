<?php

/**
 * Add widgets.
 */

namespace Piwik\Plugins\TYPO3Widgets;

class TYPO3Widgets extends \Piwik\Plugin
{
    public function addWidgets()
    {
        WidgetsList::add('TYPO3 Widgets', 'TYPO3 All News', 'TYPO3Widgets', 'rssAllNews');
        WidgetsList::add('TYPO3 Widgets', 'TYPO3 All Team News', 'TYPO3Widgets', 'rssAllTeamNews');
        WidgetsList::add('TYPO3 Widgets', 'TYPO3 Community', 'TYPO3Widgets', 'rssCommunity');
        WidgetsList::add('TYPO3 Widgets', 'TYPO3 Content Rendering Group', 'TYPO3Widgets', 'rssContentRenderingGroup');
        WidgetsList::add('TYPO3 Widgets', 'TYPO3 Development', 'TYPO3Widgets', 'rssDevelopment');
        WidgetsList::add('TYPO3 Widgets', 'TYPO3 Extensions', 'TYPO3Widgets', 'rssExtensions');
        WidgetsList::add('TYPO3 Widgets', 'TYPO3 Security', 'TYPO3Widgets', 'rssSecurity');
        WidgetsList::add('TYPO3 Widgets', 'TYPO3.Org', 'TYPO3Widgets', 'rssTypo3Org');
        WidgetsList::add('TYPO3 Widgets', 'TYPO3 Associaton', 'TYPO3Widgets', 'rssTypo3Associaton');
    }
}
