<?php

namespace Dry\ConsoleBundle\Command;
use Dry\ConsoleBundle\Helper\InputDialogFactory;
use Dry\ConsoleBundle\Helper\ScriptLoader;
use Dry\ConsoleBundle\Helper\UserInputDialog;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AskUserInputCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('ask:user')
            ->setDescription('Ask user for inputting parameters and generate the final data file')
            ->addArgument('inputFilePath', InputArgument::REQUIRED, 'JSON data file')
            ->addArgument('outputDirPath', InputArgument::REQUIRED, 'output directory path');

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $inputFilePath = $input->getArgument('inputFilePath');
        $outputDirPath = $input->getArgument('outputDirPath');
        $fileContent = file_get_contents($inputFilePath);
        $inputConfigJSON = json_decode($fileContent, true);
        $fields = array();
        if(isset($inputConfigJSON["fields"])) {
            $fields = $inputConfigJSON["fields"];
        }
        $inputData = array();
        if(!empty($fields)) {
            $dialog = $this->getHelperSet()->get('dialog');
            $userInputDialog = new UserInputDialog($input, $output,$dialog);
            $inputData = $userInputDialog->askForInput($fields);
        }
        $inputDataJSONString = json_encode($inputData);
        file_put_contents($outputDirPath.'/data.json', $inputDataJSONString);
    }

}