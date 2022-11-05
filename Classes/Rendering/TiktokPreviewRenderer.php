<?php
declare(strict_types=1);

namespace Ayacoo\Tiktok\Rendering;

use TYPO3\CMS\Backend\Preview\StandardContentPreviewRenderer;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Backend\View\BackendLayout\Grid\GridColumnItem;
use TYPO3\CMS\Core\Resource\ProcessedFileRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class TiktokPreviewRenderer
 */
class TiktokPreviewRenderer extends StandardContentPreviewRenderer
{
    public function renderPageModulePreviewContent(GridColumnItem $item): string
    {
        $content = '';
        $row = $item->getRecord();
        if ($row['bodytext']) {
            $content = $this->linkEditContent($this->renderText($row['bodytext']), $row);
        }

        if ($row['assets']) {
            // $processedFileRepository = GeneralUtility::makeInstance(ProcessedFileRepository::class);

            $content .= '<br/><br/><h6><strong>Assets</strong></h6>';
            $fileReferences = BackendUtility::resolveFileReferences('tt_content', 'assets', $row);
            foreach ($fileReferences as $fileReferenceObject) {
                // Do not show previews of hidden references
                if ($fileReferenceObject->getProperty('hidden')) {
                    continue;
                }
                $fileObject = $fileReferenceObject->getOriginalFile();
                if (!$fileObject->isMissing()) {
                    $content .= '<a href="' . $fileObject->getPublicUrl() . '" target="_blank">';
                    $content .= htmlspecialchars($fileObject->getProperty('title') . ' (@' . $fileObject->getProperty('tiktok_username') . ')');

                    // use latest processed file (64px)
                    /**
                     * $processedFiles = $processedFileRepository->findAllByOriginalFile($fileObject);
                     * foreach ($processedFiles as $processedFile) {
                     * $content .= '<br/><img src="' . $processedFile->getPublicUrl() . '" />';
                     * break;
                     * }
                     */

                    // use original thumbnail and control the size
                    $image = $fileObject->getMetaData()->offsetGet('tiktok_thumbnail') ?? '';
                    $content .= '<br/><img style="height: 100px;" src="' . htmlspecialchars($image) . '" />';

                    $content .= '</a>';
                    $content .= '<hr/>';
                }
            }
        }

        return $content;
    }
}
