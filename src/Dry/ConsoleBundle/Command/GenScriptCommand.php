<?php

namespace Dry\ConsoleBundle\Command;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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
        $templatePath = __DIR__. '/../Resources/scripts';
        $templateFilePath = $templatePath.'/'.$templateAlias.'/'.$templateAlias.'.twig';
        $templateContent = file_get_contents($templateFilePath);
        if($templateContent === false) {
            $output->writeln("The template script does not exist");
            return;
        }
        $fields = $this->getFieldsOfScript($templatePath, $templateAlias);

        $dataFilePath = $input->getArgument('dataFilePath');
        if(!empty($dataFilePath)) {
            $params = $this->parseInputFromDataFilePath($fields, $dataFilePath, $input, $output);
        } else {
            $params = $this->askUserForInput($fields, $input, $output);
        }

        $loader = new \Twig_Loader_String();
        $twig = new \Twig_Environment($loader);
        $content = $twig->render($templateContent, $params);
        $outputScriptDir = $this->getContainer()->get('kernel')->getRootDir()."/..";
        $outputScriptFile = $outputScriptDir.'/run-scripts/'.$templateAlias.'.sh';
        file_put_contents($outputScriptFile, $content);
        $output->writeln("Complete generating script");
    }

    private function getFieldsOfScript($templatePath, $templateAlias) {
        $scriptDataFilePath = $templatePath.'/'.$templateAlias.'/config.json';
        $scriptDataContent = file_get_contents($scriptDataFilePath);
        $params = array();
        if($scriptDataContent === false) {
            return $params;
        }
        $scriptDataJSON = json_decode($scriptDataContent, true);
        if(isset($scriptDataJSON["fields"])) {
            $fields = $scriptDataJSON["fields"];
        } else {
            $fields = array();
        }
        return $fields;
    }

    private function parseInputFromDataFilePath($dataFilePath, InputInterface $input, OutputInterface $output) {
        $dataContent = file_get_contents($dataFilePath);
        $params = json_decode($dataContent, true);
        //TODO need validation + throw exception if data is not followed the constraint of fields.
        return $params;
    }

    private function askUserForInput($fields, InputInterface $input, OutputInterface $output) {
        $params = array();
        $dialog = $this->getHelperSet()->get('dialog');
        foreach($fields as $field) {
            $fieldName = $field["name"];
            $title = $field["title"];
            if($field["required"] == "yes") {
                do {
                    $params[$fieldName] = $dialog->ask($output, $title.' : ');
                    if(empty($params[$fieldName])) {
                        $output->writeln("This field is required. Please input again!");
                    }
                } while(empty($params[$fieldName]));
            } else {
                $params[$fieldName] = $dialog->ask($output, $title.' : ');
            }
        }
        return $params;
    }
}