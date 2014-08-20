<?php

namespace Dry\ConsoleBundle\Helper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


abstract class InputDialog {
    protected $input;
    protected $output;
    protected $dialog;
    function __construct(InputInterface $input, OutputInterface $output, $dialog) {
        $this->input = $input;
        $this->output = $output;
        $this->dialog = $dialog;
    }
    public abstract function askForInput($fields);

} 