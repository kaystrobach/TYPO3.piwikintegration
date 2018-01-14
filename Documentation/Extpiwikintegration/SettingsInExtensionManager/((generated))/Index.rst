.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../../Includes.txt
.. include:: Images.txt


.. _generated:

((generated))
^^^^^^^^^^^^^

.. _experimental-independent-mode-enableindependentmode:

EXPERIMENTAL independent mode [enableIndependentMode]
"""""""""""""""""""""""""""""""""""""""""""""""""""""

EXT:piwikintegration contains a basic tracking JS; please don't enable
this mode in EM if you have installed EXT:piwik, it will result in
doubled Trackingcode in the Frontend.


.. _experimental-scheduler-task-enableschedulertask:

EXPERIMENTAL scheduler task [enableSchedulerTask]
"""""""""""""""""""""""""""""""""""""""""""""""""

This option installs a Scheduler task that speeds up rendering of
Matomo, as the statistics are build before viewing them in the browser.
See related chapter of this document to get more information how to
use this feature


.. _debugging-of-scheduler-task-enableschedulerloging:

Debugging of scheduler task [enableSchedulerLoging]
"""""""""""""""""""""""""""""""""""""""""""""""""""

As described in the extension manager this flag enables detailed
logging for the Matomo cronjob in the sys\_log table.

Enable heavy logging for the Matomo cronjob, should be off in
production environments as this can result in very large tables.
Please use this function for debugging only.

|img-10|

