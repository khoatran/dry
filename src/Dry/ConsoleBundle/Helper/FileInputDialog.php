<?php


namespace Dry\ConsoleBundle\Helper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


class FileInputDialog extends InputDialog{


    public function askForInput($fields){
        $dataFilePath = $this->input->getArgument('dataFilePath');
        $params = array();
        if(empty($dataFilePath)) {
            return $params;
        }

        $dataContent = file_get_contents($dataFilePath);
        $params = json_decode($dataContent, true);
        //TODO need validation + throw exception if data is not followed the constraint of fields.
        return $params;
    }
} 