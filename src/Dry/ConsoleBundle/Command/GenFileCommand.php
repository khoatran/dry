<?php

namespace Dry\ConsoleBundle\Command;
use Dry\ConsoleBundle\Helper\InputDialogFactory;
use Dry\ConsoleBundle\Helper\ScriptLoader;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenFileCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('gen-file')
            ->setDescription('Generate a file based on a template')
            ->addArgument('templatePath', InputArgument::REQUIRED, 'Template of bash script file')
            ->addArgument('destFilePath', InputArgument::REQUIRED, 'Destination file path')
            ->addArgument('dataFile', InputArgument::OPTIONAL, 'JSON data file');

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

    }

}