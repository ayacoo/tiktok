<?php

declare(strict_types=1);

namespace Ayacoo\Tiktok\Event;

use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Filelist\Event\ProcessFileListActionsEvent;

final class FileListEventListener
{
    protected IconFactory $iconFactory;

    public function __construct()
    {
        $this->iconFactory = GeneralUtility::makeInstance(IconFactory::class);
    }

    public function __invoke(ProcessFileListActionsEvent $event): void
    {
        $actionItems = $event->getActionItems();

        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        $pageRenderer->loadJavaScriptModule('@tiktok/updater.js');
        $pageRenderer->addInlineLanguageLabelFile('EXT:tiktok/Resources/Private/Language/locallang.xlf');

        $fileOrFolderObject = $event->getResource();
        if ($fileOrFolderObject instanceof File) {
            $fileProperties = $fileOrFolderObject->getProperties();
            $extension = $fileProperties['extension'] ?? '';

            $registeredHelpers = $GLOBALS['TYPO3_CONF_VARS']['SYS']['fal']['onlineMediaHelpers'] ?? [];
            if (!array_key_exists($extension, $registeredHelpers)) {
                return;
            }

            $actionItems['tiktok'] = '<a href="#" class="btn btn-default t3js-filelist-tiktok'
                . '" data-filename="' . htmlspecialchars($fileOrFolderObject->getName())
                . '" data-file-uid="' . $fileProperties['uid']
                . '" title="' . $this->getLanguageService()->getLL('tiktok.update') . '">'
                . $this->iconFactory->getIcon('actions-refresh', Icon::SIZE_SMALL)->render() . '</a>';
        }

        $event->setActionItems($actionItems);
    }

    /**
     * @return LanguageService
     */
    protected function getLanguageService(): LanguageService
    {
        $languageService = $GLOBALS['LANG'];
        $languageService->includeLLFile('EXT:tiktok/Resources/Private/Language/locallang.xlf');

        return $languageService;
    }
}
