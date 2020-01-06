<?php

declare(strict_types=1);

namespace Markup\NeedleBundle\Console;

use Markup\NeedleBundle\Indexer\CorpusIndexingCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * A Symfony console command that performs an export of a given corpus.
 */
class ExportCorpusCommand extends Command
{
    protected static $defaultName = 'markup:needle:corpus:export';

    /**
     * @var CorpusIndexingCommand
     */
    private $indexer;

    public function __construct(CorpusIndexingCommand $indexer)
    {
        $this->indexer = $indexer;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Performs an export to a defined Needle corpus.')
            ->addArgument('corpus', InputArgument::REQUIRED, 'The corpus to export')
            ->addOption('append', 'a', InputOption::VALUE_NONE, 'Append to an existing indexed corpus');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $consoleLogger = new ConsoleLogger($output);
        $this->indexer
            ->setLogger($consoleLogger);

        $corpus = $input->getArgument('corpus');
        $corpus = (is_string($corpus)) ? $corpus : '';

        $shouldAppend = (bool) $input->getOption('append');

        $io = new SymfonyStyle($input, $output);
        $io->writeln(sprintf('Exporting Needle corpus "%s"...', $corpus));
        $io->progressStart();

        $progressCallback = function () use ($io) {
            $io->progressAdvance();
        };
        $this->indexer->setPerSubjectCallback($progressCallback);
        $this->indexer->setShouldPreDelete(!$shouldAppend);

        ($this->indexer)($corpus);

        $io->progressFinish();

        $io->writeln('...done!');

        return 0;
    }
}
