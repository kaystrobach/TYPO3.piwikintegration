.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../../Includes.txt



.. _manual-installation-draft-may-not-be-complete-not-recommend:

Manual installation (draft may not be complete, not recommend)
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

To integrate piwikintegration into your TYPO3 installation follow
these steps:

#. Install the extension via TER, piwik is not included!

#. Install and enable the EXT:piwik which is suggested, to get the
   Trackingcode automatically.You do not need to configure EXT:piwik it's
   configured with the static template of EXT:piwikintegration (see
   below).Have a look in
   `http://typo3.org/extensions/repository/view/piwik/current/
   <http://typo3.org/extensions/repository/view/piwik/current/>`_ for
   setting up advanced options of EXT:piwik. You do not need to do this
   here.You do not need to install EXT:piwik, if you integrate the
   trackingcode on your own. But you need to setup up the siteid like
   described in
   `http://typo3.org/extensions/repository/view/piwik/current/
   <http://typo3.org/extensions/repository/view/piwik/current/>`_ in the
   EXT:piwik manual. This is needed, because EXT:piwikintegration uses
   the same setup as EXT:piwik

#. Install and enable the plugin EXT:piwikintegration.

#. Copy Piwik in typo3conf/piwik/piwik so that the index.php is
   accessable via typo3conf/piwik/piwik/index.php

#. Setup Piwik with the same database parameters as you use for your
   TYPO3 installation. You need to set the prefix to
   “tx\_piwikintegration\_“ as this is required for EXT:piwikintegration.

#. Open the statistik module, click on the page, or a subpage, where you
   have added the static template. The backendmodule should now recognize
   that Piwik is correctly installed (by checking if the config file
   exists). This will show the overview page.

#. Don't forget to install the TYPO3\* plugins in Piwik application (Web
   -> Statistics -> Direct Statistics Access -> Admin -> Plugins),
   especially the TYPO3Auth plugin is mandatory for authentification
   against the TYPO3 session. If this works you can disable the Login
   plugin.

#. If you like to use GeoIPDatabase use the admin functions of the module
   and download the database from maxmind.

That's all you need to start tracking your visitors. See EXT:piwik
manual for more config options and description of it's parameters.

