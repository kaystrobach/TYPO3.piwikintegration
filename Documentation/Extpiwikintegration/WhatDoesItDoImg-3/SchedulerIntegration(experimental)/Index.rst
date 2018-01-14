.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../../Includes.txt
.. include:: Images.txt


.. _scheduler-integration-experimental:

Scheduler integration (Experimental)
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

In TYPO3 4.3 there is a new extension called Scheduler (found in Admin
tools / Scheduler). With that extension installed you can [what does
the Matomo Archive Cron Job do and why should I use it?]. Use it like
this:

|img-8|

#. Create a new task by clicking on the New icon

#. Setup an intervall like daily (=86400 seconds)

If you select the Matomo task and hit Execute selected tasks, you do
not need to wait to see the statistics.

Have a look on https://matomo.org/docs/setup-auto-archiving/ to get more
information on speeding up Matomo. (ignore the cronjob, as this is
executed by the Scheduler).

Please have a look on these options in the Matomo configuration, with
the following setting you force Matomo to create the statistics with
the cronjob and not while opening Matomo in the browser, that will
speedup using Matomo.

:code:`[General]time\_before\_today\_archive\_considered\_outdated =
3600enable\_browser\_archiving\_triggering = false`

