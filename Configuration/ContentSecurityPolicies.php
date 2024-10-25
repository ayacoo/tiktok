<?php

declare(strict_types=1);

use TYPO3\CMS\Core\Security\ContentSecurityPolicy\Directive;
use TYPO3\CMS\Core\Security\ContentSecurityPolicy\Mutation;
use TYPO3\CMS\Core\Security\ContentSecurityPolicy\MutationCollection;
use TYPO3\CMS\Core\Security\ContentSecurityPolicy\MutationMode;
use TYPO3\CMS\Core\Security\ContentSecurityPolicy\Scope;
use TYPO3\CMS\Core\Security\ContentSecurityPolicy\SourceScheme;
use TYPO3\CMS\Core\Security\ContentSecurityPolicy\UriValue;
use TYPO3\CMS\Core\Type\Map;

return Map::fromEntries([
    Scope::backend(),

    new MutationCollection(
        // The csp extension is required for images in the PreviewRenderer when active
        new Mutation(
            MutationMode::Extend,
            Directive::ImgSrc,
            SourceScheme::data,
            new UriValue('*.tiktokcdn.com'),
        ),
        // The csp extension is required for the IFrame in the info window
        new Mutation(
            MutationMode::Extend,
            Directive::ScriptSrc,
            SourceScheme::data,
            new UriValue('*.tiktok.com'),
        ),
        new Mutation(
            MutationMode::Extend,
            Directive::ScriptSrc,
            SourceScheme::data,
            new UriValue('*.ttwstatic.com'),
        ),
        new Mutation(
            MutationMode::Extend,
            Directive::StyleSrc,
            SourceScheme::data,
            new UriValue('*.ttwstatic.com'),
        ),
        new Mutation(
            MutationMode::Extend,
            Directive::FrameSrc,
            SourceScheme::data,
            new UriValue('*.tiktok.com'),
        ),
    ),
]);
