<?php

use Ayacoo\Tiktok\Helper\TiktokHelper;
use Ayacoo\Tiktok\Rendering\TiktokRenderer;
use TYPO3\CMS\Core\Imaging\IconRegistry;
use TYPO3\CMS\Core\Resource\Rendering\RendererRegistry;
use TYPO3\CMS\Core\Utility\GeneralUtility;

defined('TYPO3') or die();

(function ($mediaFileExt) {
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['fal']['onlineMediaHelpers'][$mediaFileExt] = TiktokHelper::class;

    $rendererRegistry = GeneralUtility::makeInstance(RendererRegistry::class);
    $rendererRegistry->registerRendererClass(TiktokRenderer::class);

    $GLOBALS['TYPO3_CONF_VARS']['SYS']['FileInfo']['fileExtensionToMimeType'][$mediaFileExt] = 'video/' . $mediaFileExt;
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['mediafile_ext'] .= ',' . $mediaFileExt;

    $iconRegistry = GeneralUtility::makeInstance(IconRegistry::class);
    $iconRegistry->registerFileExtension('tiktok', 'mimetypes-media-image-' . $mediaFileExt);
})('tiktok');
