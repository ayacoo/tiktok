<?php

declare(strict_types=1);

namespace Ayacoo\Tiktok\Tca\DisplayCond;

class IsTiktok
{
    /**
     * @param array<string,mixed> $parameters
     */
    public function match(array $parameters): bool
    {
        $record = $parameters['record'] ?? [];
        if (!is_array($record)) {
            return false;
        }

        return (!empty($record['tiktok_html'] ?? ''));
    }
}
