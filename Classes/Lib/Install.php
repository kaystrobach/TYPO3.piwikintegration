<?php

namespace KayStrobach\Piwikintegration\Lib;

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
 * Handles the first steps of install and returns config object for next steps.
 *
 * $Id: class.tx_piwikintegration_install.php 42880 2011-01-31 22:40:14Z kaystrobach $
 *
 * @author Kay Strobach <typo3@kay-strobach.de>
 */
class Install
{
    /**
     * cache for checking if Matomo is installed, as many functions require
     * a valid installation, otherwise problems will occur.
     */
    protected $installed = null;

    /**
     * path were Matomo will be installed.
     */
    protected $installPath = 'typo3conf/piwik/';

    /**
     * @var \KayStrobach\Piwikintegration\Lib\Install
     */
    private static $installer = null;

    /**
     * get Singleton function.
     *
     * @static
     *
     * @return \KayStrobach\Piwikintegration\Lib\Install
     */
    public static function getInstaller()
    {
        if (self::$installer == null) {
            self::$installer = new self();
        }

        return self::$installer;
    }

    /**
     * private constructor to get a singleton.
     */
    private function __construct()
    {
        try {
            $this->checkInstallation();
        } catch (\Exception $e) {
            $flashMessage = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
                'TYPO3\\CMS\\Core\Messaging\\FlashMessage',
                $e->getMessage(),
                'There was a Problem',
                \TYPO3\CMS\Core\Messaging\FlashMessage::ERROR
            );
            \TYPO3\CMS\Core\Messaging\FlashMessageQueue::addMessage($flashMessage);
        }
    }

    /**
     * @return bool
     */
    public function checkInstallation()
    {
        if (file_exists(\TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($this->installPath.'piwik/index.php'))) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return void
     */
    public function installPiwik()
    {
        try {
            $this->checkUnzip();
            $zipArchivePath = $this->downloadLatestPiwik();
            $this->extractDownloadedPiwik($zipArchivePath);
            $this->patchPiwik();
            $this->configureDownloadedPiwik();
        } catch (\Exception $e) {
            $flashMessage = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
                'TYPO3\\CMS\\Core\Messaging\\FlashMessage',
                $e->getMessage(),
                'There was a Problem',
                \TYPO3\CMS\Core\Messaging\FlashMessage::ERROR
            );
            \TYPO3\CMS\Core\Messaging\FlashMessageQueue::addMessage($flashMessage);
        }
    }

    /**
     * @return string
     */
    public function getAbsInstallPath()
    {
        return \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($this->installPath);
    }

    /**
     * @return string
     */
    public function getRelInstallPath()
    {
        return $this->installPath;
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->installPath.'piwik/';
    }

    /**
     * @throws \Exception
     *
     * @return string
     */
    private function downloadLatestPiwik()
    {
        // tell installer where to grab Matomo
        $settings = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['piwikintegration']);
        if (array_key_exists('piwikDownloadSource', $settings) && $settings['piwikDownloadSource'] != '') {
            $downloadSource = $settings['piwikDownloadSource'];
        } else {
            $downloadSource = 'https://builds.piwik.org/latest.zip';
        }

        //download Matomo into typo3temp
        $zipArchivePath = \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName('typo3temp/piwiklatest.zip');
        \TYPO3\CMS\Core\Utility\GeneralUtility::writeFileToTypo3tempDir(
            $zipArchivePath,
            \TYPO3\CMS\Core\Utility\GeneralUtility::getURL($downloadSource)
        );

        if (@filesize($zipArchivePath) === false) {
            throw new \Exception('Installation invalid, typo3temp '.$zipArchivePath.' can´t be created for some reason');
        }
        if (@filesize($zipArchivePath) < 10) {
            throw new \Exception('Installation invalid, typo3temp'.$zipArchivePath.' is smaller than 10 bytes, download definitly failed');
        }

        return $zipArchivePath;
    }

    /**
     * @param string $zipArchivePath
     *
     * @throws \Exception
     *
     * @return void
     */
    private function extractDownloadedPiwik($zipArchivePath = '')
    {
        //make dir for extraction
        \TYPO3\CMS\Core\Utility\GeneralUtility::mkdir_deep(PATH_site, $this->installPath);
        if (!is_writable(PATH_site.$this->installPath)) {
            throw new \Exception($this->installPath.' must be writeable');
        }

        //extract archive
        switch ($this->checkUnzip()) {
            case 'clsZipArchive':
                $zip = new \ZipArchive();
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
                try {
                    //up to 4.4.4
                    $emUnzipFile = PATH_typo3.'/mod/tools/em/class.em_unzip.php';
                    if (file_exists($emUnzipFile)) {
                        require_once $emUnzipFile;
                    }
                    $zlibObj = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('em_unzip', $zipArchivePath);
                } catch (\Exception $e) {
                    //from 4.5.0b2
                    $zlibObj = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('tx_em_Tools_Unzip', $zipArchivePath);
                }
                $zlibObj->extract([
                    'add_path' => $this->getAbsInstallPath(),
                ]);
                break;
            default:
                throw new \Exception('There is no valid unzip wrapper, i need either the class ZipArchiv from php or a *nix system with unzip path set.');
                break;
        }
        //unlink archiv to save space in typo3temp ;)
        \TYPO3\CMS\Core\Utility\GeneralUtility::unlink_tempfile($zipArchivePath);

        if (!$this->checkInstallation()) {
            $buffer = 'No files has been extracted!';
            if (!class_exists('ZipArchive')) {
                $buffer .= ' -> Please enable the phpextensions Zip or Zlib!';
            }
            if ((!(TYPO3_OS == 'WIN' || $GLOBALS['TYPO3_CONF_VARS']['BE']['disable_exec_function']))) {
                $buffer .= ' -> used TYPO3 cmd line function to extract files, if you use solaris this may be the problem.';
                $buffer .= ' -> please manually extract Matomo and copy it to typo3conf/piwik/piwik and use the extmgm update script to patch and configure piwik';
                $buffer .= ' -> take a look in your manual for more information or use an environment with a working zip class';
            }

            throw new \Exception($buffer);
        }
    }

    /**
     * @return bool
     */
    public function checkPiwikPatched()
    {
        $_EXTKEY = 'piwikintegration';
        $piwikPatchVersion = '0.0.0';
        $EM_CONF = [];
        @include \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('piwikintegration').'ext_emconf.php';
        @include $this->getAbsInstallPath().'/piwik/piwikintegration.php';
        if ($EM_CONF['piwikintegration']['version'] != $piwikPatchVersion) {
            return false;
        }

        return true;
    }

    /**
     * @param array $exclude
     *
     * @throws \Exception
     */
    public function patchPiwik($exclude = [])
    {
        if (!is_writable($this->getAbsInstallPath())) {
            throw new \Exception('Installation is invalid, '.$this->getAbsInstallPath().' was not writeable for applying the patches');
        }
        //recursive directory copy is not supported under windows ... so i implement is myself!!!
        $source = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('piwikintegration').'piwik_patches/';
        $dest = $this->getAbsInstallPath().'piwik/';
        $cmd = [];
        $t = \TYPO3\CMS\Core\Utility\GeneralUtility::getAllFilesAndFoldersInPath(
            [],
            $source,
            '',
            true,
            99
        );
        foreach ($t as $entry) {
            $shortEntry = str_replace($source, '', $entry);
            if ($shortEntry != '' && $shortEntry != '.') {
                if (!in_array($shortEntry, $exclude)) {
                    if (is_dir($entry)) {
                        $cmd['newfolder'][] = [
                            'data'   => basename($shortEntry),
                            'target' => dirname($dest.$shortEntry),
                        ];
                        @mkdir($dest.$shortEntry);
                    } elseif (is_file($entry)) {
                        $cmd['copy'][] = [
                            'data'   => $entry,
                            'target' => $dest.$shortEntry,
                        ];
                        @copy($entry, $dest.$shortEntry);
                    }
                }
            }
        }
        //store information about the last patch process
        $_EXTKEY = 'piwikintegration';
        $EM_CONF = [];
        @include \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('piwikintegration').'ext_emconf.php';
        $data = '<?php $piwikPatchVersion = "'.$EM_CONF['piwikintegration']['version'].'"; '.chr(63).'>';
        file_put_contents($this->getAbsInstallPath().'piwik/piwikintegration.php', $data);
    }

    /**
     * @throws \Exception
     */
    private function configureDownloadedPiwik()
    {
        $this->getConfigObject()->makePiwikConfigured();
    }

    /**
     * @throws \Exception
     *
     * @return \KayStrobach\Piwikintegration\Lib\Config
     */
    public function getConfigObject()
    {
        if ($this->checkInstallation()) {
            return \KayStrobach\Piwikintegration\Lib\Config::getConfigObject();
        } else {
            throw new \Exception('Matomo is not installed!');
        }
    }

    /**
     * @return bool
     */
    public function removePiwik()
    {
        return \TYPO3\CMS\Core\Utility\GeneralUtility::rmdir($this->getAbsInstallPath(), true);
    }

    /**
     * @throws \Exception
     *
     * @return string
     */
    public function checkUnzip()
    {
        if (class_exists('ZipArchive')) {
            return 'clsZipArchive';
        } elseif (extension_loaded('zlib')) {
            return 'zlib';
        } elseif (!(TYPO3_OS == 'WIN' || $GLOBALS['TYPO3_CONF_VARS']['BE']['disable_exec_function'])) {
            return 'cmd';
        } else {
            throw new \Exception('There is no valid unzip wrapper, i need either the class ZipArchiv from php or a *nix system with unzip path set.');
        }
    }
}
