<?php

declare(strict_types=1);

namespace Ayacoo\Tiktok\Command;

use Ayacoo\Tiktok\Domain\Repository\FileRepository;
use Ayacoo\Tiktok\Helper\TiktokHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CMS\Core\Resource\Index\MetaDataRepository;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class UpdateMetadataCommand extends Command
{
    protected function configure(): void
    {
        $this->setDescription('Updates the Tiktok metadata');
        $this->addOption(
            'limit',
            null,
            InputOption::VALUE_OPTIONAL,
            'Defines the number of Tiktok videos to be checked',
            10
        );
    }

    public function __construct(
        protected FileRepository     $fileRepository,
        protected MetaDataRepository $metadataRepository,
        protected ResourceFactory    $resourceFactory
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $limit = (int)($input->getOption('limit') ?? 10);

        $tiktokHelper = GeneralUtility::makeInstance(TiktokHelper::class, 'tiktok');

        $videos = $this->fileRepository->getVideosByFileExtension('tiktok', $limit);
        foreach ($videos as $video) {
            $file = $this->resourceFactory->getFileObject($video['uid']);
            $metaData = $tiktokHelper->getMetaData($file);
            if (!empty($metaData)) {
                $this->metadataRepository->update($file->getUid(), [
                    'width' => (int)$metaData['width'],
                    'height' => (int)$metaData['height'],
                    'title' => $metaData['title'] ?? '',
                    'author' => $metaData['author'],
                    'tiktok_html' => $metaData['tiktok_html'],
                    'tiktok_author_url' => $metaData['tiktok_author_url'],
                    'tiktok_thumbnail' => $metaData['tiktok_thumbnail'],
                    'tiktok_username' => $metaData['tiktok_username']
                ]);
                $io->success($file->getProperty('title') . '(UID: ' . $file->getUid() . ') was processed');
            }
        }

        return Command::SUCCESS;
    }
}
