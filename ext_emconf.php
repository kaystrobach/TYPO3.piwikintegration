<?php

$EM_CONF[$_EXTKEY] = [
    'title'            => 'Matomo Backend integration',
    'description'      => 'Uses EXT:piwik to inserts Data in the HTML header and gives BE-Users the right to see the data for their sites. Autoupdate of Matomo will work as TYPO3-Admin!',
    'category'         => 'module',
    'version'          => '5.0.0',
    'module'           => 'mod1',
    'state'            => 'beta',
    'clearcacheonload' => 0,
    'author'           => 'Kay Strobach',
    'author_email'     => 'kay.strobach@typo3.org',
    'author_company'   => '',
    'constraints'      => [
        'depends' => [
            'php'   => '7.2.0-8.0.99',
            'typo3' => '10.0.0-11.5.99',
        ],
        'conflicts' => [
            'dbal' => '1.0.0-99.0.0',
        ],
        'suggests' => [
            'piwik' => '2.0.0-4.999.0',
        ],
    ],
];
