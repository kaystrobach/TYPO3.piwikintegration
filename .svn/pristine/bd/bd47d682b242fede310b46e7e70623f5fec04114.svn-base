/***************************************************************
*  Copyright notice
*
*  (c) 2010 Kay Strobach (typo3@kay-strobach.de)
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*  A copy is found in the textfile GPL.txt and important notices to the license
*  from the author is found in LICENSE.txt distributed with these scripts.
*
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * mod1/extjs.js
 *
 * backendviewport
 *
 * $Id$
 *
 * @author Kay Strobach <typo3@kay-strobach.de>
 */

Ext.onReady(function() {

	Ext.QuickTips.init();
	var piwikViewport = new Ext.Viewport({
		layout:'fit',
		renderTo:Ext.getBody(),
		items:[
			{
				bbar:[
					{
						xtype:'panel',
						html:'Piwik siteid[<b>###siteId###</b>]'
					},'->',{
						xtype:'panel',
						html:'<a onClick="window.open(\'http://www.kay-strobach.de\');">&copy;KS</a>'
					},'-',{
						xtype:'panel',
						html:'<a onClick="window.open(\'http://typo3.org/extensions/repository/view/piwikintegration/current/\');">Powered by piwikintegration</a>'
					}	
				],
				tbar:new Ext.Toolbar({
					cls:'x-piwikintegration-toolbar',
					items:[
						{
							tooltip:'Fullscreen',
							iconCls:'x-piwikintegration-btn-fullscreen',
							handler:function() {
								win = new top.Ext.Window({
									title:'Piwik',
									html:'<iframe src="../typo3conf/piwik/piwik/index.php?module=CoreHome&action=index&period=week&date=yesterday&idSite=###siteId###" width="100%" height="100%" frameborder="0"></iframe>',
									modal:true,
									maximized:true,
									closeable:true,
									resizable:false
								}).show();
							}
						},{
							tooltip:'###piwikApiTab###',
							iconCls: 'x-piwikintegration-btn-settings',
							handler:function() {
								win = new top.Ext.Window({
									title:'###piwikApiTab###',
									html:'###piwikApiContent###',
									autoScroll:true,
									width:600,
									height:350,
									modal:true,
									closeaction:'close'
								});
								win.show();
							}
						},'->',
						{
							tooltip:'Docu',
							iconCls:'x-piwikintegration-btn-docs-api-1',
							xtype:'tbbutton',
							menu:[
								{
									text:'API Documentation',
									iconCls:'x-piwikintegration-btn-docs-api-1',
									handler:function() {
										window.open('http://dev.piwik.org/trac/wiki/API/Reference');
									}
								},'-',{
									text:'Tracker Documentation',
									iconCls:'x-piwikintegration-btn-docs-api-2',
									handler:function() {
										window.open('http://piwik.org/docs/javascript-tracking/');
									}
								},{
									text:'Goaltracker Documentation',
									iconCls:'x-piwikintegration-btn-docs-api-3',
									handler:function() {
										window.open('http://piwik.org/docs/tracking-goals-web-analytics/');
									}
								},{
									text:'Advanced Tracking Documentation',
									iconCls:'x-piwikintegration-btn-docs-api-4',
									handler:function() {
										window.open('http://piwik.org/docs/tracking-api/');
									}
								}
							]
						}
					]
				}),
			
				html:'<iframe src="../typo3conf/piwik/piwik/index.php?module=CoreHome&action=index&period=week&date=yesterday&idSite=###siteId###" width="100%" height="100%" frameborder="0"></iframe>',
				//title: '###piwikTab###',
				bodyStyle:'padding:0;margin:0'
			}
		]
	});
	Ext.get('typo3-docbody').remove(); 
});