<?php

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

if (!defined('TYPO3')) {
    die('Access denied.');
}

$additionalColumns = [
    'tiktok_thumbnail' => [
        'exclude' => true,
        'label' => 'LLL:EXT:tiktok/Resources/Private/Language/locallang_db.xlf:sys_file_metadata.tiktok_thumbnail',
        'config' => [
            'type' => 'text',
            'dbType' => 'text',
            'cols' => 40,
            'rows' => 2,
            'readOnly' => true,
        ],
        'displayCond' => 'USER:Ayacoo\\Tiktok\\Tca\\DisplayCond\\IsTiktok->match',
    ],
    'tiktok_html' => [
        'exclude' => true,
        'label' => 'LLL:EXT:tiktok/Resources/Private/Language/locallang_db.xlf:sys_file_metadata.tiktok_html',
        'config' => [
            'type' => 'text',
            'dbType' => 'text',
            'cols' => 40,
            'rows' => 4,
            'readOnly' => true,
        ],
        'displayCond' => 'USER:Ayacoo\\Tiktok\\Tca\\DisplayCond\\IsTiktok->match',
    ],
    'tiktok_author_url' => [
        'exclude' => true,
        'label' => 'LLL:EXT:tiktok/Resources/Private/Language/locallang_db.xlf:sys_file_metadata.tiktok_author_url',
        'config' => [
            'type' => 'link',
            'allowedTypes' => ['url'],
            'default' => '',
            'readOnly' => true,
            'size' => 40,
        ],
        'displayCond' => 'USER:Ayacoo\\Tiktok\\Tca\\DisplayCond\\IsTiktok->match',
    ],
    'tiktok_username' => [
        'exclude' => true,
        'label' => 'LLL:EXT:tiktok/Resources/Private/Language/locallang_db.xlf:sys_file_metadata.tiktok_username',
        'config' => [
            'type' => 'input',
            'size' => 40,
            'max' => 255,
            'default' => '',
            'readOnly' => true,
        ],
        'displayCond' => 'USER:Ayacoo\\Tiktok\\Tca\\DisplayCond\\IsTiktok->match',
    ],
];

ExtensionManagementUtility::addTCAcolumns('sys_file_metadata', $additionalColumns);
ExtensionManagementUtility::addToAllTCAtypes(
    'sys_file_metadata',
    '--div--;LLL:EXT:tiktok/Resources/Private/Language/locallang_db.xlf:tab.tiktok, tiktok_thumbnail, tiktok_html, tiktok_author_url, tiktok_username'
);
