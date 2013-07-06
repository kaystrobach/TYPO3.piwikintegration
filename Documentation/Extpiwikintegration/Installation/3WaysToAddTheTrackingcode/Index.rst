.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../../Includes.txt



.. _3-ways-to-add-the-trackingcode:

3 ways to add the trackingcode
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

Basically there 3 ways to install the trackingcode to your site


.. _the-recommend-way-ext-piwik:

The recommend way: EXT:piwik
""""""""""""""""""""""""""""

Ext:piwik adds the trackingcode to your Page. All you need to do is to
download and install EXT:piwik via the TYPO3 extension manager. Add
the static TS of EXT:piwik and EXT:piwikintegration to your page
template and configure the idsite. See
`http://typo3.org/extensions/repository/view/piwik/current/
<http://typo3.org/extensions/repository/view/piwik/current/>`_ for
more details.

This way offers an easy configuration interface via EXT:piwik TS
interface:

- .piwik\_idsite

- .piwik\_host

- .piwik\_action\_name

- .piwik\_download\_extensions

- .piwik\_hosts\_alias

- .piwik\_tracker\_pause

- .piwik\_install\_tracker

Note: there is already a version 2.x in the SVN on forge.typo3.org
which is still waiting to get published:

http://forge.typo3.org/repositories/show/extension-piwik


.. _the-manual-way:

The manual way
""""""""""""""

Use EXT:piwikintegration to obtain the JS Code. Add it to your TS
pagetemplate. Configure config.tx\_piwik.piwik\_idsite, because
Ext:piwikintegration needs that value for setting up the users.


.. _the-debug-way:

The debug way
"""""""""""""

EXT:piwikintegration is shipped with a basic trackingcode add script.
You may enable it in the extension manager. And add the static TS of
EXT:piwikintegration to your TS template. See the chapter “Settings in
Extensionmanager” for more details. It's not recommend to use that
option, as there is only one configuration option:
config.tx\_piwik.piwik\_idsite.

