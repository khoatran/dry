<?php

namespace Dry\ConsoleBundle\Helper;


class ScriptLoader {
    protected $fields;
    protected $scriptTemplate;
    protected $errors = array();
    protected $isLoadedSuccess = false;

    public function getErrors() {
        return $this->errors;
    }

    public function isSuccess() {
        return $this->isLoadedSuccess;
    }

    public function getFields() {
        return $this->fields;
    }

    public function getScriptTemplate() {
        return $this->scriptTemplate;
    }

    public function load($scriptPath, $templateAlias) {
        $this->isLoadedSuccess = false;


        $templateFilePath = $scriptPath.'/'.$templateAlias.'/'.$templateAlias.'.twig';
        $scriptConfigFilePath = $scriptPath.'/'.$templateAlias.'/config.json';

        $this->scriptTemplate = file_get_contents($templateFilePath);
        if($this->scriptTemplate === false) {
            $this->errors[] = "The template script does not exist";
            return;
        }
        $this->loadFieldOfScript($scriptConfigFilePath);
        $this->isLoadedSuccess = true;
    }

    private function loadFieldOfScript($scriptConfigFilePath) {

        $scriptConfigContent = file_get_contents($scriptConfigFilePath);
        $scriptConfigJSON = json_decode($scriptConfigContent, true);
        if(isset($scriptConfigJSON["fields"])) {
            $this->fields = $scriptConfigJSON["fields"];
        } else {
            $this->fields = array();
        }

    }
} 