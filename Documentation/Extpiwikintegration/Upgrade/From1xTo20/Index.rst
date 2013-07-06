

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


From 1.x to 2.0
^^^^^^^^^^^^^^^

Simply upgrade via TER, update the TS Setup with the new Option, after
opening the BE Module piwik gets installed and you're done.

Don't forget to install EXT:piwik for automated adding of the
trackingcode. → Please check your page for the trackingcode as non
logged in BE user, as EXT:piwik from version 2.x won't log
authenticated BE Users.

