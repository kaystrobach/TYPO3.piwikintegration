<?php
// Register icons not being part of TYPO3.Icons repository
return [
    'piwikintegration-icon' => [
        'provider' => \TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider::class,
        'source' => 'EXT:piwikintegration/Resources/Public/Icons/ce_wiz.gif'
    ],
];
