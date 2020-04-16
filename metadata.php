<?php
/**
 * Metadata version
 */

use StainlessPlugins\OxidCookieConsentBase\Core\JavaScriptRegistrator;
use StainlessPlugins\OxidCookieConsentBase\Core\JavaScriptRenderer;
use StainlessPlugins\OxidCookieConsentBase\Core\UtilsView;

$sMetadataVersion = '2.1';

/**
 * Module information
 */
$aModule = [
    'id'          => 'sp-cookieconsentbase',
    'title'       => [
        'de' => 'Cookie Consent Basis Module',
        'en' => 'Cookie Consent Base Module',
    ],
    'description' => [
        'de' => 'Basis Modul für die Integration von Cookie Consent Tools.',
        'en' => 'Basic Module for cookie constent tools.',
    ],
    'thumbnail'   => 'logo.png',
    'version'     => '1.0',
    'author'      => 'stainlessplugins',
    'url'         => 'http://stainlesspluginsmcom',
    'email'       => 'info@stainlessplugins.com',
    'extend'      => [
        \OxidEsales\Eshop\Core\ViewHelper\JavaScriptRenderer::class =>
            JavaScriptRenderer::class,
        \OxidEsales\Eshop\Core\ViewHelper\JavaScriptRegistrator::class =>
            JavaScriptRegistrator::class,

        \OxidEsales\Eshop\Core\UtilsView::class => UtilsView::class,
    ],
    'smartyPluginDirectories' => [
        'SmartyPlugins',
    ],
    'templates' => [
    ],
    'settings' => [
        [
            'name' => 'license',
            'value' => '',
            'group' => 'main',
            'type' => 'str'
        ]

    ]
];
