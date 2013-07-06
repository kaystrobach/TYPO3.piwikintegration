

.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. ==================================================
.. DEFINE SOME TEXTROLES
.. --------------------------------------------------
.. role::   underline
.. role::   typoscript(code)
.. role::   ts(typoscript)
   :class:  typoscript
.. role::   php(code)


Automated Installation (recommend)
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

To integrate piwikintegration into your TYPO3 installation follow
these steps:

#. Install the extension via TER, piwik is not included!

#. Also install and enable the EXT:piwik which is suggested, to get the
   trackingcode automatically.You do  **not** need to configure EXT:piwik
   since it is already configured with the static template of
   EXT:piwikintegration (see below).Have a look in
   `http://typo3.org/extensions/repository/view/piwik/current/
   <http://typo3.org/extensions/repository/view/piwik/current/>`_ for
   setting up advanced options of EXT:piwik. You do not need to do this
   here.

#. Enable the extension EXT:piwikintegration.

#. Add the static template of EXT:piwikintegration (choose Web ->
   Template -> [page where you have your template] -> Info/Modify -> Edit
   whole template record -> Includes) This will enable a single idsite
   and add the needed ts-configuration for EXT:piwik, as described above.

#. Open the statistik module (Web -> Statistics ) and click on the page,
   or a subpage, where you added the static template:
   
   #. The backendmodule now recognizes, that Piwik is not installed (by
      checking, if the config file exists).
   
   #. The backendmodule will now download, extract and install the latest
      Piwik release into typo3conf/piwik. This could take a while.
   
   #. The backendmodule will now reload and you will now able to use Piwik
      via the Direct Statistics Access dropdown box.

#. (If you like to use GeoIPDatabase use the admin functions of the
   module and download the database from maxmind.)[This function is
   currently not available.]

That's all you need to start tracking your visitors. See EXT:piwik
manual for more config options and description of it's parameters.

