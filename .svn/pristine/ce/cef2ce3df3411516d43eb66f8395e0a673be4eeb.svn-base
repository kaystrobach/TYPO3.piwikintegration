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
 * Handles the first steps of install and returns config object for next steps
 *
 * $Id: class.tx_piwikintegration_install.php 42880 2011-01-31 22:40:14Z kaystrobach $
 *
 * @author Kay Strobach <typo3@kay-strobach.de>
 */
 
 
class tx_piwikintegration_install {
	/**
	 * cache for checking if piwik is installed, as many functions require
	 * a valid installation, otherwise problems will occur.	 
	 */	 	
	protected $installed   = null;
	/**
	 * path were piwik will be installed
	 */	 	
	protected $installPath = 'typo3conf/piwik/';

	/**
	 * @var tx_piwikintegration_install
	 */
	private static $installer   = null;
	/**
	 * get Singleton function
	 * @static
	 * @return tx_piwikintegration_install
	 */
	static function getInstaller() {
		if(self::$installer == null) {
			self::$installer = new tx_piwikintegration_install();
		}
		return self::$installer;
	}
	/**
	 * private constructor to get a singleton
	 */	 	
	private function __construct() {
		try {
			$this->checkInstallation();
		} catch(Exception $e) {
			$flashMessage = t3lib_div::makeInstance(
				't3lib_FlashMessage',
				$e->getMessage(),
				'There was a Problem',
				t3lib_FlashMessage::ERROR
		    );
			t3lib_FlashMessageQueue::addMessage($flashMessage);
		}
	}
	/**
	 *
	 */
	public function checkInstallation() {
		if(file_exists(t3lib_div::getFileAbsFileName($this->installPath.'piwik/index.php'))) {
			return true;
		} else {
			return false;
		}
	}
	public function installPiwik() {
		try {
			$this->checkUnzip();
			$zipArchivePath=$this->downloadLatestPiwik();
			$this->extractDownloadedPiwik($zipArchivePath);
			$this->patchPiwik();
			$this->configureDownloadedPiwik();
		} catch(Exception $e) {
			$flashMessage = t3lib_div::makeInstance(
				't3lib_FlashMessage',
				$e->getMessage(),
				'There was a Problem',
				t3lib_FlashMessage::ERROR
		    );
			t3lib_FlashMessageQueue::addMessage($flashMessage);
		}
	}
	public function getAbsInstallPath() {
		return t3lib_div::getFileAbsFileName($this->installPath);
	}
	public function getRelInstallPath() {
		return $this->installPath;
	}
	public function getBaseUrl() {
		return $this->installPath.'piwik/';
	}
	private function downloadLatestPiwik() {
		GLOBAL $TYPO3_CONF_VARS;

		// tell installer where to grab piwik
		$settings = unserialize($TYPO3_CONF_VARS['EXT']['extConf']['piwikintegration']);
		if(array_key_exists('piwikDownloadSource', $settings) && $settings['piwikDownloadSource'] != '') {
			$downloadSource = $settings['piwikDownloadSource'];
		} else {
			$downloadSource = 'http://piwik.org/latest.zip';
		}

		//download piwik into typo3temp
		$zipArchivePath = t3lib_div::getFileAbsFileName('typo3temp/piwiklatest.zip');
		t3lib_div::writeFileToTypo3tempDir($zipArchivePath,t3lib_div::getURL($downloadSource));
		if(@filesize($zipArchivePath)===FALSE) {
			throw new Exception('Installation invalid, typo3temp '.$zipArchivePath.' can´t be created for some reason');
		}
		if(@filesize($zipArchivePath)<10) {
			throw new Exception('Installation invalid, typo3temp'.$zipArchivePath.' is smaller than 10 bytes, download definitly failed');
		}
		return $zipArchivePath;
	}
	private function extractDownloadedPiwik($zipArchivePath) {
		//make dir for extraction
			t3lib_div::mkdir_deep(PATH_site,$this->installPath);
			if(!is_writeable(PATH_site.$this->installPath)) {
				throw new Exception($this->installPath.' must be writeable');
			}
		//extract archive
			switch($this->checkUnzip()) {
				case 'clsZipArchive':
					$zip = new ZipArchive();
					$zip->open($zipArchivePath);
					$zip->extractTo($this->getAbsInstallPath());
					$zip->close();
					unset($zip);
				break;
				case 'cmd':
					$cmd = $GLOBALS['TYPO3_CONF_VARS']['BE']['unzip_path'].'unzip -qq "'.$zipArchivePath.'" -d "'.$this->getAbsInstallPath().'"';
					exec($cmd);
				break;
				case 'zlib':
					try{
						//up to 4.4.4
						$emUnzipFile = PATH_typo3.'/mod/tools/em/class.em_unzip.php';
						if(file_exists($emUnzipFile)) {
							require_once($emUnzipFile);
						}
						$zlib_obj = t3lib_div::makeInstance('em_unzip',$zipArchivePath);
					} catch(Exception $e) {
						//from 4.5.0b2
						$zlib_obj = t3lib_div::makeInstance('tx_em_Tools_Unzip',$zipArchivePath);
					}
					$zlib_obj->extract(array(
						'add_path' => $this->getAbsInstallPath()
					));
				break;
				default:
					throw new Exception('There is no valid unzip wrapper, i need either the class ZipArchiv from php or a *nix system with unzip path set.');
				break;
			}
		//unlink archiv to save space in typo3temp ;)
			t3lib_div::unlink_tempfile($zipArchivePath);
		if(!$this->checkInstallation()) {
			$buffer = 'No files has been extracted!';
			if(!class_exists('ZipArchive')) {
				$buffer.= ' -> Please enable the phpextensions Zip or Zlib!';
			}
			if((!(TYPO3_OS=='WIN' || $GLOBALS['TYPO3_CONF_VARS']['BE']['disable_exec_function']))) {
				$buffer.= ' -> used TYPO3 cmd line function to extract files, if you use solaris this may be the problem.';
				$buffer.= ' -> please manually extract piwik and copy it to typo3conf/piwik/piwik and use the extmgm update script to patch and configure piwik';
				$buffer.= ' -> take a look in your manual for more information or use an environment with a working zip class';
			}
			throw new Exception($buffer);
		}

	}
	public function checkPiwikPatched() {
		$_EXTKEY = 'piwikintegration';
		$piwikPatchVersion = '0.0.0';
		$EM_CONF = array();
		@include(t3lib_extMgm::extPath('piwikintegration').'ext_emconf.php');
		@include($this->getAbsInstallPath().'/piwik/piwikintegration.php');
		if($EM_CONF['piwikintegration']['version'] != $piwikPatchVersion) {
			return false;
		}
		return true;
	}
	public function patchPiwik($exclude=array()) {
		if(!is_writeable($this->getAbsInstallPath())) {
			throw new Exception('Installation is invalid, '.$this->getAbsInstallPath().' was not writeable for applying the patches');
		}
		//recursive directory copy is not supported under windows ... so i implement is myself!!!
		$source = t3lib_extMgm::extPath('piwikintegration').'piwik_patches/';
		$dest   = $this->getAbsInstallPath().'piwik/';
		$cmd    = array();
		$t = t3lib_div::getAllFilesAndFoldersInPath(
			array(),
			$source,
			'',
			true,
			99
		);
		foreach($t as $entry) {
			$shortEntry = str_replace($source,'',$entry);
			if($shortEntry!='' && $shortEntry!='.') {
				if(!in_array($shortEntry, $exclude)) {
					if(is_dir($entry)) {
						$cmd['newfolder'][] = array(
							'data'   => basename($shortEntry),
							'target' => dirname($dest.$shortEntry),
						);
						@mkdir($dest.$shortEntry);
					} elseif(is_file($entry)) {
						$cmd['copy'][] = array(
							'data'   => $entry,
							'target' => $dest.$shortEntry,
						);
						@copy($entry,$dest.$shortEntry);
					}
				}
			}
		}
		//store information about the last patch process
		$_EXTKEY = 'piwikintegration';
		$EM_CONF = array();
		@include(t3lib_extMgm::extPath('piwikintegration').'ext_emconf.php');
		$data = '<?php $piwikPatchVersion = "'.$EM_CONF['piwikintegration']['version'].'"; '.chr(63).'>';
		file_put_contents($this->getAbsInstallPath().'piwik/piwikintegration.php',$data);
	}
	private function configureDownloadedPiwik() {
		$this->getConfigObject()->makePiwikConfigured();
	}
    /**
     * @throws Exception
     * @return tx_piwikintegration_config
     */
	public function getConfigObject() {
		if($this->checkInstallation()) {
			include_once(t3lib_extMgm::extPath('piwikintegration', 'Classes/Lib/Config.php'));
			return tx_piwikintegration_config::getConfigObject();
		} else {
			throw new Exception('Piwik is not installed!');
		}
	}
	public function removePiwik() {
		return t3lib_div::rmdir($this->getAbsInstallPath(),true);
	}
	public function checkUnzip() {
		if(class_exists('ZipArchive')) {
				return 'clsZipArchive';
			} elseif(extension_loaded('zlib')) {
				return 'zlib';
			} elseif(!(TYPO3_OS=='WIN' || $GLOBALS['TYPO3_CONF_VARS']['BE']['disable_exec_function']))	{
				return 'cmd';
			} else {
				throw new Exception('There is no valid unzip wrapper, i need either the class ZipArchiv from php or a *nix system with unzip path set.');
			}
	}
}