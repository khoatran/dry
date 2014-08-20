<?php

namespace Dry\ConsoleBundle\Command;
use Dry\ConsoleBundle\Helper\InputDialogFactory;
use Dry\ConsoleBundle\Helper\ScriptLoader;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenScriptCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('gen-script')
            ->setDescription('Create the bash script based on a template')
            ->addArgument('template', InputArgument::REQUIRED, 'Template of bash script file')
            ->addArgument('dataFilePath', InputArgument::OPTIONAL, 'JSON data file - if you don\'t want to input data manually');


    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $templateAlias = $input->getArgument('template');
        $scriptLoader = new ScriptLoader();
        $scriptLoader->load($templateAlias);


        if(!$scriptLoader->isSuccess()) {
            $errors = $scriptLoader->getErrors();
            foreach($errors as $error) {
                $output->writeln($error);
            }
            return;
        }

        $dialog = $this->getHelperSet()->get('dialog');
        $templateContent = $scriptLoader->getScriptTemplate();

        $consoleInputDialog = InputDialogFactory::getFieldInputDialog($input, $output, $dialog);
        $fields = $scriptLoader->getFields();
        $params = $consoleInputDialog->askForInput($fields);

        $loader = new \Twig_Loader_String();
        $twig = new \Twig_Environment($loader);
        $content = $twig->render($templateContent, $params);
        $outputScriptDir = $this->getContainer()->get('kernel')->getRootDir()."/..";
        $outputScriptFile = $outputScriptDir.'/run-scripts/'.$templateAlias.'.sh';
        file_put_contents($outputScriptFile, $content);
        $output->writeln("Complete generating script");
    }

}