.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../../Includes.txt



.. _generated:

((generated))
^^^^^^^^^^^^^

.. _generated:

((generated))
"""""""""""""

.. _what-do-i-have-to-do-if-the-installation-fails-with-there-is-no-valid-unzip-wrapper-i-need-either-the-class-ziparchiv-from-php-or-a-nix-system-with-unset-path-set:

What do I have to do if the installation fails with:There is no valid unzip wrapper, i need either the class ZipArchiv from php or a \*nix system with unset path set.
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

You have 3 opinions:

#. Manual setup → hard

#. Install Zip Extension for PHP

#. Do not use Microsoft Windows and disable
   $GLOBALS['TYPO3\_CONF\_VARS']['BE']['disable\_exec\_function'] in the
   TYPO3 Install tool.


.. _use-with-ks-sitemgr-currently-not-published-in-ter:

Use with ks\_sitemgr (currently not published in TER)
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Use the customer id constant to setup the trackingcode – example will
be provided later.


.. _what-to-do-if-piwik-can-t-update:

What to do if Matomo can't update?
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

There are 2 possible solutions

#. Manually unpack the archive into typo3conf/piwik

#. Make a list of installed plugins and settings, delete the
   typo3conf/piwik directory, make a clean install and reconfigure Piwik

