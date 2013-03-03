<?php
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
 * mod1/index.php
 *
 * backendmodule
 *
 * $Id$
 *
 * @author Kay Strobach <typo3@kay-strobach.de>
 * 
 * Contributors:
 * Dimitry König (point to missing correctUserRights for PID in 3.0.x)   
 */


$LANG->includeLLFile('EXT:piwikintegration/mod1/locallang.xml');
require_once(PATH_t3lib . 'class.t3lib_scbase.php');
require_once(PATH_t3lib . 'class.t3lib_page.php');
require_once(t3lib_extMgm::extPath('piwikintegration').'lib/class.tx_piwikintegration_install.php');
require_once(t3lib_extMgm::extPath('piwikintegration').'lib/class.tx_piwikintegration_div.php');
require_once(t3lib_extMgm::extPath('piwikintegration').'lib/class.tx_piwikintegration_tracking.php');
$BE_USER->modAccess($MCONF,1);	// This checks permissions and exits if the users has no permission for entry.
// DEFAULT initialization of a module [END]



/**
 * Module 'Statistics' for the 'piwikintegration' extension.
 *
 * @author	Kay Strobach <info@kay-strobach.de>
 * @package	TYPO3
 * @subpackage	tx_piwikintegration
 */
	class  tx_piwikintegration_module1 extends t3lib_SCbase {
		var $pageinfo;

		/**
		 * Initializes the Module
 		 *
		 * @return	void
		 */
		function init()	{
			global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;
			$this->piwikHelper = t3lib_div::makeInstance('tx_piwikintegration_div');
			parent::init();

			/*
			if (t3lib_div::_GP('clear_all_cache'))	{
				$this->include_once[] = PATH_t3lib.'class.t3lib_tcemain.php';
			}
			*/
		}

		/**
		 * Adds items to the ->MOD_MENU array. Used for the function menu selector.
		 *
		 * @return	void
		 */
		function menuConfig()	{
			global $LANG;
			$this->MOD_MENU = Array (
				'function' => Array (
					'1' => $LANG->getLL('function1'),
					'2' => $LANG->getLL('function2'),
					'3' => $LANG->getLL('function3'),
				)
			);
			parent::menuConfig();
		}

		/**
		 * Main function of the module. Write the content to $this->content
		 * If you chose "web" as main module, you will need to consider the $this->id parameter which will contain the uid-number of the page clicked in the page tree
		 *
		 * @return	void
		 */
		function main()	{
			global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

			// Access check!
			// The page will show only if there is a valid page and if this page may be viewed by the user
			$this->pageinfo = t3lib_BEfunc::readPageAccess($this->id,$this->perms_clause);
			$access = is_array($this->pageinfo) ? 1 : 0;			// initialize doc
			$this->doc = t3lib_div::makeInstance('template');
			$this->doc->backPath = $BACK_PATH;

			$set = t3lib_div::_GP('SET');

			$this->doc->setModuleTemplate(t3lib_extMgm::extPath('piwikintegration') . 'mod1/mod_template.html');
			$this->doc->getPageRenderer()->loadExtJS();
			$this->doc->getPageRenderer()->addCssFile(t3lib_extMgm::extRelPath('piwikintegration') . 'mod1/ext-icons.css');
			if($this->content = $this->checkEnvironment()) {

				if(version_compare ($GLOBALS['TYPO_VERSION'],'4.3.0','>=')) {
					$this->content = '';
					$tracker       = new tx_piwikintegration_tracking();
					$piwikSiteId   = $this->piwikHelper->getPiwikSiteIdForPid($this->id);
					$this->piwikHelper->correctUserRightsForSiteId($piwikSiteId);
					$this->piwikHelper->correctTitle($this->id,$piwikSiteId,$this->piwikHelper->getPiwikConfigArray($this->id));
					$this->doc->extJScode = file_get_contents(t3lib_extMgm::extPath('piwikintegration') . 'mod1/extjs.js');
					$this->doc->extJScode = str_replace('###piwikTab###'       ,$LANG->getLL('piwikTab')    ,$this->doc->extJScode);
					$this->doc->extJScode = str_replace('###piwikApiTab###'    ,$LANG->getLL('piwikApiTab') ,$this->doc->extJScode);
					$this->doc->extJScode = str_replace('###piwikApiContent###',$this->getPiwikApi()        ,$this->doc->extJScode);
					$this->doc->extJScode = str_replace('###siteId###'         ,$piwikSiteId                ,$this->doc->extJScode);
				} else {
					$this->content = '<h3>Fallback Mode for older TYPO3 versions, you need at least 4.3 to use all features</h3>';
					$this->content.= '<iframe width="100%" height="80%" src="../typo3conf/piwik/piwik"></iframe>';
				}
			}
			
			if (($this->id && $access) || ($BE_USER->user['admin'] && !$this->id))	{

					// Draw the form
				$this->doc->form = '<form action="" method="post" enctype="multipart/form-data" name="editForm">';

				$this->doc->postCode='
					<script language="javascript" type="text/javascript">
						script_ended = 1;
						if (top.fsMod) top.fsMod.recentIds["web"] = 0;
					</script>
				';
					// Render content:
			} else {
					// If no access or if ID == zero
				$docHeaderButtons['save'] = '';
				$this->content.=$this->doc->spacer(10);
			}

				// compile document
			$markers['CONTENT'] = $this->content;
					// Build the <body> for the module
			$this->content = $this->doc->startPage($LANG->getLL('title'));
			$this->content.= $this->doc->moduleBody($this->pageinfo, $docHeaderButtons, $markers);
			$this->content.= $this->doc->endPage();
			$this->content = $this->doc->insertStylesAndJS($this->content);

		}
		function getPiwikApi() {
			global $BE_USER;
			require_once(t3lib_extMgm::extPath('piwikintegration').'lib/class.tx_piwikintegration_tracking.php');
			$content.='<h3>Piwik API</h3>';
			$content.='Your API Code: '.$BE_USER->user['tx_piwikintegration_api_code'].'<br />';
			$content.='Your Piwik URL: '.tx_piwikintegration_install::getInstaller()->getBaseUrl().'<br />';
			$content.='<h3>JavaScriptCode for Piwik</h3>';
			$tracker = new tx_piwikintegration_tracking();
			$content.='<p><code style="border:1px solid gray;padding:20px;display:block;">'.$tracker->getPiwikJavaScriptCodeForPid($this->pageinfo['uid']).'</code></p>';
			return str_replace("\n",'',addslashes(($content)));
		}
		/**
		 * Prints out the module HTML
		 *
		 * @return	void
		 */
		function printContent()	{
			#$this->content.=$this->doc->endPage();
			echo $this->content;
		}

		/**
		 * Generates the module content
		 *
		 * @return	void
		 */
		function checkEnvironment()	{
			global $BACK_PATH,$TYPO3_CONF_VARS, $BE_USER,$LANG;
			//check if piwik is installed
			
			if(!tx_piwikintegration_install::getInstaller()->checkInstallation()) {
				tx_piwikintegration_install::getInstaller()->installPiwik();
				if(tx_piwikintegration_install::getInstaller()->checkInstallation()) {
					$flashMessage = t3lib_div::makeInstance(
					    't3lib_FlashMessage',
					    'Piwik installed',
					    'Piwik is now installed / upgraded, wait a moment, reload the page ;)',
					    t3lib_FlashMessage::OK
					);
					t3lib_FlashMessageQueue::addMessage($flashMessage);
				}
				return;
			} elseif(!$this->pageinfo['uid']) {
			    $flashMessage = t3lib_div::makeInstance(
					't3lib_FlashMessage',
					$LANG->getLL('selectpage_description'),
					$LANG->getLL('selectpage_tip'),
					t3lib_FlashMessage::NOTICE
			    );
			    t3lib_FlashMessageQueue::addMessage($flashMessage);
			    #$this->doc->pushFlashMessage($flashMessage);
			    return;
			} elseif($this->piwikHelper->getPiwikSiteIdForPid($this->pageinfo['uid'])) {
				if(!tx_piwikintegration_install::getInstaller()->checkPiwikPatched()) {
					//prevent lost configuration and so the forced repair.
					$exclude = array(
						'config/config.ini.php',
					);
					tx_piwikintegration_install::getInstaller()->patchPiwik($exclude);
				}
				return true;
			} else {
				$flashMessage = t3lib_div::makeInstance(
					't3lib_FlashMessage',
					$LANG->getLL('selectpage_description'),
					$LANG->getLL('selectpage_tip'),
					t3lib_FlashMessage::NOTICE
			    );
			    t3lib_FlashMessageQueue::addMessage($flashMessage);
			}
			return false;
		}
		/**
		 * Create the panel of buttons for submitting the form or otherwise perform operations.
		 *
		 * @return	array		all available buttons as an assoc. array
		 */
		protected function getButtons()	{

			$buttons = array(
				'csh' => '',
				'shortcut' => '',
				'save' => ''
			);
				// CSH
			$buttons['csh'] = t3lib_BEfunc::cshItem('_MOD_web_func', '', $GLOBALS['BACK_PATH']);

				// SAVE button
			#$buttons['save'] = '<input type="image" class="c-inputButton" name="submit" value="Update"' . t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], 'gfx/savedok.gif', '') . ' title="' . $GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_core.php:rm.saveDoc', 1) . '" />';


				// Shortcut
			if ($GLOBALS['BE_USER']->mayMakeShortcut())	{
				$buttons['shortcut'] = $this->doc->makeShortcutIcon('', 'function', $this->MCONF['name']);
			}

			return $buttons;
		}
	}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/piwikintegration/mod1/index.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/piwikintegration/mod1/index.php']);
}




// Make instance:
$SOBE = t3lib_div::makeInstance('tx_piwikintegration_module1');
$SOBE->init();

// Include files?
foreach($SOBE->include_once as $INC_FILE)	include_once($INC_FILE);

$SOBE->main();
$SOBE->printContent();

?>