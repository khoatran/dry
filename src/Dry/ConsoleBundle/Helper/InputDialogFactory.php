<?php


namespace Dry\ConsoleBundle\Helper;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class InputDialogFactory {
    public static function getFieldInputDialog(InputInterface $input, OutputInterface $output, $dialog) {
        $dataFilePath = $input->getArgument('dataFilePath');
        if(!empty($dataFilePath)) {
            return new FileInputDialog($input, $output, $dialog);
        }
        return new UserInputDialog($input, $output, $dialog);
    }

} 