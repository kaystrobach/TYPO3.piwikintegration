.. include:: Images.txt

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

EXPERIMENTAL independent mode [enableIndependentMode]
"""""""""""""""""""""""""""""""""""""""""""""""""""""

EXT:piwikintegration contains a basic tracking JS; please don't enable
this mode in EM if you have installed EXT:piwik, it will result in
doubled Trackingcode in the Frontend.


EXPERIMENTAL scheduler task [enableSchedulerTask]
"""""""""""""""""""""""""""""""""""""""""""""""""

This option installs a Scheduler task that speeds up rendering of
Piwik, as the statistics are build before viewing them in the browser.
See related chapter of this document to get more information how to
use this feature


Debugging of scheduler task [enableSchedulerLoging]
"""""""""""""""""""""""""""""""""""""""""""""""""""

As described in the extension manager this flag enables detailed
logging for the piwik cronjob in the sys\_log table.

Enable heavy logging for the piwik cronjob, should be off in
production environments as this can result in very large tables.
Please use this function for debugging only.

|img-10|

