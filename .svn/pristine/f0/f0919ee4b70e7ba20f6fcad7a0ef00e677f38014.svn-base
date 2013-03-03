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
class Piwik_TYPO3Login extends Piwik_Plugin
{
	/**
	 * get extension information
	 *
	 * @return	array		with information
	 */
	public function getInformation()
	{
		include(PIWIK_INCLUDE_PATH.'/piwikintegration.php');
		return array(
			'name' => 'TYPO3Login',
			'description' => 'TYPO3 Auth Login plugin. It uses the TYPO3 session and permission data to grant access to users on piwik.',
			'author' => 'Kay Strobach',
			'homepage' => 'http://kay-strobach.de/',
		    'version' => $piwikPatchVersion,
			);
	}

	/**
	 * returns registered hooks
	 *
	 * @return	array		array of hooks
	 */
	function getListHooksRegistered()
	{
		$hooks = array(
			'FrontController.initAuthenticationObject'	    => 'initAuthenticationObject',
			'FrontController.NoAccessException'	            => 'noAccess',
			'API.Request.authenticate'                      => 'ApiRequestAuthenticate',
			'Login.initSession'                             => 'initSession',
		);
		return $hooks;
	}
	function noAccess( $notification )
	{
		$exception  = $notification->getNotificationObject();
		$exceptionMessage = $exception->getMessage();
		$controller = new Piwik_TYPO3Login_Controller();
		$controller->login($exceptionMessage);
	}
	/**
	 * init the authentification object
	 *
	 * @param	mixed		$notification: some data from the api, which is not needed
	 * @return	void
	 */
	function initAuthenticationObject($notification)
	{
		$auth = new Piwik_TYPO3Login_Auth();
     	Zend_Registry::set('auth', $auth);

     			$action = Piwik::getAction();
		if(Piwik::getModule() === 'API'
			&& (empty($action) || $action == 'index'))
		{
			return;
		}

		$authCookieName = Zend_Registry::get('config')->General->login_cookie_name;
		$authCookieExpiry = time() + Zend_Registry::get('config')->General->login_cookie_expire;
		$authCookie = new Piwik_Cookie($authCookieName, $authCookieExpiry);
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
		$info = $notification->getNotificationObject();
		$login = $info['login'];
		$md5Password = $info['md5Password'];
		
		$tokenAuth = Piwik_TYPO3Login_Auth::getTokenAuth($login, $md5Password);
	
		$auth = Zend_Registry::get('auth');
		$auth->setLogin($login);
		$auth->setTokenAuth($tokenAuth);

		$authResult = $auth->authenticate();

		if(!$authResult->isValid())
		{
			throw new Exception(Piwik_Translate('Login_LoginPasswordNotCorrect'));
		}
		$ns = new Zend_Session_Namespace('Piwik_Login.referer');
		unset($ns->referer);

		$authCookieName = Zend_Registry::get('config')->General->login_cookie_name;
		$authCookieExpiry = time() + Zend_Registry::get('config')->General->login_cookie_expire;
		$authCookiePath = Zend_Registry::get('config')->General->login_cookie_path;
		$cookie = new Piwik_Cookie($authCookieName, $authCookieExpiry, $authCookiePath);
		$cookie->set('login', $login);
		$cookie->set('token_auth', $tokenAuth);
		$cookie->save();

		Zend_Session::regenerateId();
	}
	function ApiRequestAuthenticate($notification)
	{
		$tokenAuth = $notification->getNotificationObject();
		Zend_Registry::get('auth')->setLogin($login = null);
		Zend_Registry::get('auth')->setTokenAuth($tokenAuth);
	}

}
?>