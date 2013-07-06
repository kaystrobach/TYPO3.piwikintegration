.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt



.. _security:

Security
--------

DO NOT use the same database movement within different TYPO3
installations where non trusted customers on the same database:

If you do have more than one customer which are allowed to access the
same piwikdatabase, than this could be a great security problem. This
could occur, if you give your customers admin rights in TYPO3 and use
only one Piwik.

Solution: use a different Piwik database for all your customers.


