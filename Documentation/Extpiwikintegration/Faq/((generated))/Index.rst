

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


((generated))
^^^^^^^^^^^^^

((generated))
"""""""""""""

What do I have to do if the installation fails with:There is no valid unzip wrapper, i need either the class ZipArchiv from php or a \*nix system with unset path set.
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

You have 3 opinions:

#. Manual setup → hard

#. Install Zip Extension for PHP

#. Do not use Microsoft Windows and disable
   $GLOBALS['TYPO3\_CONF\_VARS']['BE']['disable\_exec\_function'] in the
   TYPO3 Install tool.


Use with ks\_sitemgr (currently not published in TER)
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Use the customer id constant to setup the trackingcode – example will
be provided later.


What to do if Piwik can't update?
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

There are 2 possible solutions

#. Manually unpack the archive into typo3conf/piwik

#. Make a list of installed plugins and settings, delete the
   typo3conf/piwik directory, make a clean install and reconfigure Piwik

