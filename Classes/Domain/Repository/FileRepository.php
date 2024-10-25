<?php

declare(strict_types=1);

namespace Ayacoo\Tiktok\Domain\Repository;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\AbstractPlatform as DoctrineAbstractPlatform;
use Doctrine\DBAL\Platforms\MariaDBPlatform as DoctrineMariaDBPlatform;
use Doctrine\DBAL\Platforms\MySQLPlatform as DoctrineMySQLPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform as DoctrinePostgreSQLPlatform;
use Doctrine\DBAL\Platforms\SQLitePlatform as DoctrineSQLitePlatform;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class FileRepository
{
    private const SYS_FILE_TABLE = 'sys_file';

    private ?AbstractPlatform $platform;

    public function getVideosByFileExtension(string $extension, int $limit = 0): array
    {
        $queryBuilder = $this->getQueryBuilder(self::SYS_FILE_TABLE);

        $whereConstraints = [];
        $whereConstraints[] = $queryBuilder->expr()->eq(
            'extension',
            $queryBuilder->createNamedParameter(strtolower($extension))
        );
        $whereConstraints[] = $queryBuilder->expr()->eq(
            'missing',
            $queryBuilder->createNamedParameter(0, Connection::PARAM_INT)
        );

        $version = $this->getPlatformIdentifier($this->platform);
        $randomFunction = match ($version) {
            'mysql', 'pdo_mysql', 'drizzle_pdo_mysql' => 'RAND()',
            default => 'random()',
        };

        $statement = $queryBuilder
            ->select('*')
            ->addSelectLiteral($randomFunction . ' AS randomnumber')
            ->from(self::SYS_FILE_TABLE)
            ->where(...$whereConstraints)
            ->orderBy('randomnumber');

        if ($limit > 0) {
            $statement->setMaxResults($limit);
        }

        return $queryBuilder->executeQuery()->fetchAllAssociative();
    }

    protected function getQueryBuilder(string $tableName = ''): QueryBuilder
    {
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        $connection = $connectionPool->getConnectionForTable($tableName);

        $this->platform = $connection->getDatabasePlatform();

        return $connection->createQueryBuilder();
    }

    protected function getPlatformIdentifier(DoctrineAbstractPlatform $platform): string
    {
        if ($platform instanceof DoctrineMariaDBPlatform) {
            return 'mysql';
        }
        if ($platform instanceof DoctrineMySQLPlatform) {
            return 'mysql';
        }
        if ($platform instanceof DoctrinePostgreSqlPlatform) {
            return 'postgresql';
        }
        if ($platform instanceof DoctrineSQLitePlatform) {
            return 'sqlite';
        }
        throw new \RuntimeException(
            'Unsupported Databaseplatform "' . get_class($platform) . '" detected in PlatformInformation',
            1500958070
        );
    }
}
