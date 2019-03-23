# Integrates Matomo into the TYPO3 Backend

## This version of piwikintegration is compatible with Matomo 3.8 - 3.9.

![Build Status](https://travis-ci.org/kaystrobach/TYPO3.piwikintegration.svg)
[![StyleCI](https://styleci.io/repos/8537360/shield?branch=master)](https://styleci.io/repos/8537360)

See why the build is currently failing, most of the issues are CGL things, will be fixed soon.

https://travis-ci.org/kaystrobach/TYPO3.piwikintegration/

# Plan for the future development of this extension

* remove the Matomo installer from the extension source
* use Matomo api to connect to Matomo instances 
* provide an easy to use "dashboard"-like view in the TYPO3 backend, which shows the most important values from Matomo
* maybe allow some kind of single-sign-on from TYPO3 to Matomo with standard plugins like saml or openid

if you are interested in these changes, please contact me, we are currently collecting sponsors for that development.

# Updating piwikintegration

## to 5.x future

* major api changes due to switch to namespaces

## to 4.x

Due to the switch to extbase these 2 things have to be taken into account

* Please recheck that the static template of piwikintegration is still included after upgrading to 4.x
* If the BE module of piwikintegration had been made accessible for BE users or groups, then it will now no longer be accessible and it must be made accessible in the BE user/group record again. 
