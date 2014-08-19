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
            ->addArgument('template', InputArgument::REQUIRED, 'Template of bash script file');

        ;
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

        $params = $this->askUserForInput($templatePath, $templateAlias, $input, $output);
        $loader = new \Twig_Loader_String();
        $twig = new \Twig_Environment($loader);
        $content = $twig->render($templateContent, $params);
        $outputScriptDir = $this->getContainer()->get('kernel')->getRootDir()."/..";
        $outputScriptFile = $outputScriptDir.'/run-scripts/'.$templateAlias.'.sh';
        $result = file_put_contents($outputScriptFile, $content);
        $output->writeln("Complete generating script");
    }

    private function askUserForInput($templatePath, $templateAlias, InputInterface $input, OutputInterface $output) {
        $scriptDataFilePath = $templatePath.'/'.$templateAlias.'/'.$templateAlias.'.json';
        $scriptDataContent = file_get_contents($scriptDataFilePath);
        $params = array();
        if($scriptDataContent !== false) {
            $dialog = $this->getHelperSet()->get('dialog');
            $scriptDataJSON = json_decode($scriptDataContent, true);
            $fields = $scriptDataJSON["fields"];
            foreach($fields as $field) {
                $fieldName = $field["name"];
                $title = $field["title"];
                $params[$fieldName] = $dialog->ask($output, $title.' : ');
            }
        }
        return $params;
    }
}