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
 * piwik_patches/plugins/TYPO3Login/TYPO3Login.php
 *
 * FE controller class
 *
 * $Id: TYPO3Login.php 40947 2010-12-08 07:05:46Z kaystrobach $
 *
 * @author Kay Strobach <typo3@kay-strobach.de>
 */
namespace Piwik\Plugins\TYPO3Login;

require PIWIK_INCLUDE_PATH.'/plugins/TYPO3Login/Auth.php';


/**
 * Class for authentification plugin
 * 
 * @author  Kay Strobach <typo3@kay-strobach.de>
 * @link http://kay-strobach.de
 * @license http://www.gnu.org/licenses/gpl-3.0.html Gpl v3 or later
 *
 * @package Piwik_TYPO3Login
 */
class TYPO3Login extends \Piwik\Plugin
{

	/**
	 * returns registered hooks
	 *
	 * @return	array		array of hooks
	 */
	function getListHooksRegistered()
	{
		$hooks = array(
			'Request.initAuthenticationObject'	    => 'initAuthenticationObject',
			'User.isNotAuthorized'	            => 'noAccess',
			'API.Request.authenticate'                      => 'ApiRequestAuthenticate',
			'AssetManager.getJavaScriptFiles'  => 'getJsFiles'
		);
		return $hooks;
	}

	public function getJsFiles(&$jsFiles)
	{
		$jsFiles[] = "plugins/Login/javascripts/login.js";
	}

	function noAccess( $notification )
	{
		$exception  = $notification->getNotificationObject();
		$exceptionMessage = $exception->getMessage();
		$controller = new \Piwik\Plugins\TYPO3Login\Controller();
		$controller->login($exceptionMessage);
	}
	/**
	 * init the authentification object
	 *
	 * @return	void
	 */
	function initAuthenticationObject()
	{
		$config = \Piwik\Config::getInstance();
		
		$auth = new \Piwik\Plugins\TYPO3Login\Auth();
        \Piwik\Registry::set('auth', $auth);

     	$action = \Piwik\Piwik::getAction();
		if(\Piwik\Piwik::getModule() === 'API'
			&& (empty($action) || $action == 'index'))
		{
			return;
		}

		$authCookieName = $config->General['login_cookie_name'];
		$authCookieExpiry = time() + $config->General['login_cookie_expire'];
		$authCookie = new \Piwik\Cookie($authCookieName, $authCookieExpiry);
		$defaultLogin = 'anonymous';
		$defaultTokenAuth = 'anonymous';
		if($authCookie->isCookieFound())
		{
			$defaultLogin = $authCookie->get('login');
			$defaultTokenAuth = $authCookie->get('token_auth');
		}
		$auth->setLogin($defaultLogin);
		$auth->setTokenAuth($defaultTokenAuth);
	}
	function initSession($notification)
	{
		$config = \Piwik\Config::getInstance();
		
		$info = $notification->getNotificationObject();
		$login = $info['login'];
		$md5Password = $info['md5Password'];
		
		$tokenAuth = \Piwik\Plugins\TYPO3Login\Auth::getTokenAuth($login, $md5Password);
	
		$auth = \Zend_Registry::get('auth');
		$auth->setLogin($login);
		$auth->setTokenAuth($tokenAuth);

		$authResult = $auth->authenticate();

		if(!$authResult->isValid())
		{
			/** @todo find translator */
			throw new \Exception('Login_LoginPasswordNotCorrect');
		}
		$ns = new \Zend_Session_Namespace('Piwik_Login.referer');
		unset($ns->referer);

		$authCookieName = $config->General['login_cookie_name'];
		$authCookieExpiry = time() + $config->General['login_cookie_expire'];
		$authCookiePath = $config->General['login_cookie_path'];
		$cookie = new \Piwik\Cookie($authCookieName, $authCookieExpiry, $authCookiePath);
		$cookie->set('login', $login);
		$cookie->set('token_auth', $tokenAuth);
		$cookie->save();

		\Zend_Session::regenerateId();
	}
	function ApiRequestAuthenticate($tokenAuth)
	{
		\Piwik\Registry::get('auth')->setLogin($login = null);
		\Piwik\Registry::get('auth')->setTokenAuth($tokenAuth);
	}

}
