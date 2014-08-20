<?php


namespace Dry\ConsoleBundle\Helper;


class UserInputDialog extends InputDialog{


    public function askForInput($fields){
        $params = array();
        foreach($fields as $field) {
            $fieldName = $field["name"];
            $title = $field["title"];
            if($field["required"] == "yes") {
                do {
                    $params[$fieldName] = $this->dialog->ask($this->output, $title.' : ');
                    if(empty($params[$fieldName])) {
                        $this->output->writeln("This field is required. Please input again!");
                    }
                } while(empty($params[$fieldName]));
            } else {
                $params[$fieldName] = $this->dialog->ask($this->output, $title.' : ');
            }
        }
        return $params;
    }
} 