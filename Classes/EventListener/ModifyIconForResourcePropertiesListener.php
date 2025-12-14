<?php

declare(strict_types=1);

namespace Ayacoo\Tiktok\EventListener;

use TYPO3\CMS\Core\Attribute\AsEventListener;
use TYPO3\CMS\Core\Imaging\Event\ModifyIconForResourcePropertiesEvent;
use TYPO3\CMS\Core\Resource\File;

/**
 * Adjusts the icon for resources with mime type "video/tiktok".
 */
final class ModifyIconForResourcePropertiesListener
{
    #[AsEventListener]
    public function __invoke(ModifyIconForResourcePropertiesEvent $event): void
    {
        $resource = $event->getResource();

        if (!$resource instanceof File) {
            return;
        }

        if ($resource->getMimeType() === 'video/tiktok') {
            $event->setIconIdentifier('mimetypes-media-image-tiktok');
        }
    }
}
