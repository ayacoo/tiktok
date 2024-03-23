<?php

declare(strict_types=1);

namespace Ayacoo\Tiktok\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Http\JsonResponse;
use TYPO3\CMS\Core\Resource\Exception\FileDoesNotExistException;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\Index\MetaDataRepository;
use TYPO3\CMS\Core\Resource\OnlineMedia\Helpers\OnlineMediaHelperRegistry;
use TYPO3\CMS\Core\Resource\ProcessedFileRepository;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class OnlineMediaUpdateController
{
    /**
     * AJAX endpoint for storing the URL as a sys_file record
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws FileDoesNotExistException
     */
    public function updateAction(ServerRequestInterface $request): ResponseInterface
    {
        if ($request->getMethod() !== 'POST') {
            return new JsonResponse(['Please use a POST request'], 500);
        }

        $parsedBody = $request->getParsedBody();
        $uid = $parsedBody['uid'];
        $resourceFactory = GeneralUtility::makeInstance(ResourceFactory::class);
        $file = $resourceFactory->getFileObject($uid);

        $onlineMediaViewHelper = GeneralUtility::makeInstance(OnlineMediaHelperRegistry::class)
            ->getOnlineMediaHelper($file);

        // remove online media temp image
        $videoId = $onlineMediaViewHelper->getOnlineMediaId($file);
        $temporaryFileName = $this->getTempFolderPath() . $file->getExtension() . '_' . md5($videoId) . '.jpg';
        if (file_exists($temporaryFileName)) {
            unlink($temporaryFileName);
        }
        $previewPath = $onlineMediaViewHelper->getPreviewImage($file);

        $this->updateMetaData($file, $onlineMediaViewHelper->getMetaData($file));
        $this->removeProcessedFiles($file);

        return new JsonResponse(['path' => $previewPath]);
    }

    protected function getTempFolderPath(): string
    {
        $path = Environment::getPublicPath() . '/typo3temp/assets/online_media/';
        if (!is_dir($path)) {
            GeneralUtility::mkdir_deep($path);
        }
        return $path;
    }

    protected function removeProcessedFiles(File $file): void
    {
        $processedFileRepository = GeneralUtility::makeInstance(ProcessedFileRepository::class);
        $processedFiles = $processedFileRepository->findAllByOriginalFile($file);

        foreach ($processedFiles as $processedFile) {
            $processedFile->delete();
        }
    }

    /**
     * We need to get an update on the metadata
     *
     * @param File $file
     * @param array $metaData
     */
    protected function updateMetaData(File $file, array $metaData): void
    {
        $metadataRepository = GeneralUtility::makeInstance(MetaDataRepository::class);
        $metadataRepository->update($file->getUid(), [
            'width' => (int)$metaData['width'],
            'height' => (int)$metaData['height'],
            'tiktok_thumbnail' => $metaData['tiktok_thumbnail'],
        ]);
    }
}
