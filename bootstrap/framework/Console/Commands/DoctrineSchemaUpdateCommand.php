<?php

namespace WebApp\Console\Commands;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 *
 */
final class DoctrineSchemaUpdateCommand extends Command
{
    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(private readonly EntityManagerInterface $em)
    {
        parent::__construct('doctrine:schema:update');
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Update Doctrine schema (attributes mapping).')
            ->addOption('dump-sql', null, InputOption::VALUE_NONE, 'Dump the SQL statements without executing them')
            ->addOption('force', null, InputOption::VALUE_NONE, 'Execute the SQL statements to update the schema');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $dumpSql = (bool) $input->getOption('dump-sql');
        $force = (bool) $input->getOption('force');

        if (!$dumpSql && !$force) {
            $output->writeln('<comment>No action taken.</comment> Use --dump-sql or --force.');
            return Command::INVALID;
        }

        $classes = $this->em->getMetadataFactory()->getAllMetadata();
        if ($classes === []) {
            $output->writeln('<info>No Doctrine metadata found.</info> Add entities under app/Entities (attributes mapping).');
            return Command::SUCCESS;
        }

        $tool = new SchemaTool($this->em);

        if ($dumpSql) {
            $sql = $tool->getUpdateSchemaSql($classes);
            if ($sql === []) {
                $output->writeln('<info>Nothing to update.</info>');
                return Command::SUCCESS;
            }
            foreach ($sql as $stmt) {
                $output->writeln($stmt . ';');
            }
            return Command::SUCCESS;
        }

        $tool->updateSchema($classes);
        $output->writeln('<info>Schema updated.</info>');
        return Command::SUCCESS;
    }
}

