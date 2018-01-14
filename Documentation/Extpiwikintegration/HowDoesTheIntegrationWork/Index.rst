.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt



.. _how-does-the-integration-work:

How does the integration work?
------------------------------

#. User opens statistik module for a specific page → TYPO3 checks access
   → Module inserts access records to Matomo tables automatically (admin
   as superuser, normal user with view rights)

#. load Matomo in iframe / object tag

#. Authenticate user against TYPO3 be\_users table with the user of the
   be cookie. For that step the plugin TYPO3Login is needed in Matomo.

#. That's all.


