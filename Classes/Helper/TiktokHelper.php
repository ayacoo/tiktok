<?php

declare(strict_types=1);

namespace Ayacoo\Tiktok\Helper;

use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\Folder;
use TYPO3\CMS\Core\Resource\OnlineMedia\Helpers\AbstractOEmbedHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Tiktok helper class
 */
class TiktokHelper extends AbstractOEmbedHelper
{
    private const TIKTOK_URL = 'https://www.tiktok.com/';

    private const UNICODE_PATTERN = '/[\x00-\x1F\x80-\xFF]/';

    protected function getOEmbedUrl($mediaId, $format = 'json')
    {
        return sprintf(
            self::TIKTOK_URL . 'oembed?url=' . self::TIKTOK_URL . 'video/%s',
            rawurlencode($mediaId)
        );
    }

    public function transformUrlToFile($url, Folder $targetFolder)
    {
        $videoId = null;
        // Try to get the Tiktok code from given url.
        // - https://www.tiktok.com/@<username>/video/<code>?parameter # Share URL
        if (preg_match('%(?:.*)tiktok\.com\/@(?:[a-z.\-_0-9]*)\/video\/([0-9]*)%i', $url, $match)) {
            $videoId = $match[1];
        }
        if (empty($videoId)) {
            return null;
        }

        return $this->transformMediaIdToFile($videoId, $targetFolder, $this->extension);
    }

    /**
     * Transform mediaId to File
     *
     * We override the abstract function so that we can integrate our own handling for the title field
     *
     * @param string $mediaId
     * @param Folder $targetFolder
     * @param string $fileExtension
     * @return File
     */
    protected function transformMediaIdToFile($mediaId, Folder $targetFolder, $fileExtension)
    {
        $file = $this->findExistingFileByOnlineMediaId($mediaId, $targetFolder, $fileExtension);
        if ($file === null) {
            $fileName = $mediaId . '.' . $fileExtension;

            $oEmbed = $this->getOEmbedData($mediaId);
            if (!empty($oEmbed['title'])) {
                $title = $this->handleTiktokTitle($oEmbed['title']);
                if (!empty($title)) {
                    $fileName = $title . '.' . $fileExtension;
                }
            }
            $file = $this->createNewFile($targetFolder, $fileName, $mediaId);
        }
        return $file;
    }

    public function getPublicUrl(File $file)
    {
        $videoId = $this->getOnlineMediaId($file);

        $properties = $file->getProperties();
        $username = $properties['tiktok_username'] ?? '';

        return sprintf(self::TIKTOK_URL . '@' . $username . '/video/%s', rawurlencode($videoId));
    }

    public function getPreviewImage(File $file)
    {
        $properties = $file->getProperties();
        $previewImageUrl = $properties['tiktok_thumbnail'] ?? '';

        $videoId = $this->getOnlineMediaId($file);
        $temporaryFileName = $this->getTempFolderPath() . 'tiktok_' . md5($videoId) . '.jpg';

        if (!empty($previewImageUrl)) {
            $previewImage = GeneralUtility::getUrl($previewImageUrl);
            file_put_contents($temporaryFileName, $previewImage);
            GeneralUtility::fixPermissions($temporaryFileName);
            return $temporaryFileName;
        }

        return '';
    }

    /**
     * Get meta data for OnlineMedia item
     * Using the meta data from oEmbed
     *
     * @param File $file
     * @return array with metadata
     */
    public function getMetaData(File $file)
    {
        $metaData = [];

        $oEmbed = $this->getOEmbedData($this->getOnlineMediaId($file));
        if ($oEmbed) {
            $metaData['width'] = (int)$oEmbed['width'];
            $metaData['height'] = (int)$oEmbed['height'];
            $metaData['title'] = $this->handleTiktokTitle($oEmbed['title']);
            $metaData['author'] = $oEmbed['author_name'];
            $metaData['tiktok_html'] = preg_replace(self::UNICODE_PATTERN, '', $oEmbed['html']);
            $metaData['tiktok_thumbnail'] = $oEmbed['thumbnail_url'];
            $metaData['tiktok_author_url'] = $oEmbed['author_url'];
            $metaData['tiktok_username'] = str_replace(self::TIKTOK_URL . '@', '', $oEmbed['author_url']);
        }

        return $metaData;
    }

    /**
     * @param string $title
     * @return string
     */
    protected function handleTiktokTitle(string $title): string
    {
        $title = preg_replace(self::UNICODE_PATTERN, '', $title);
        return trim(mb_substr(strip_tags($title), 0, 255));
    }
}
