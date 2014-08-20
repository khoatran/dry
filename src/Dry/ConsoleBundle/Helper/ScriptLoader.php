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

    public function load($templateAlias) {
        $this->isLoadedSuccess = false;
        $templatePath = __DIR__. '/../Resources/scripts';
        $templateFilePath = $templatePath.'/'.$templateAlias.'/'.$templateAlias.'.twig';
        $this->scriptTemplate = file_get_contents($templateFilePath);
        if($this->scriptTemplate === false) {
            $this->errors[] = "The template script does not exist";
            return;
        }
        $this->loadFieldOfScript($templatePath, $templateAlias);
        $this->isLoadedSuccess = true;
    }

    private function loadFieldOfScript($templatePath, $templateAlias) {
        $scriptConfigFilePath = $templatePath.'/'.$templateAlias.'/config.json';
        $scriptConfigContent = file_get_contents($scriptConfigFilePath);
        $scriptConfigJSON = json_decode($scriptConfigContent, true);
        if(isset($scriptConfigJSON["fields"])) {
            $this->fields = $scriptConfigJSON["fields"];
        } else {
            $this->fields = array();
        }

    }
} 