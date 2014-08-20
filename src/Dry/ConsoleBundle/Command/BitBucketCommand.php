<?php

namespace Dry\ConsoleBundle\Command;
use Dry\ConsoleBundle\Helper\ConsoleConfig;
use Dry\ConsoleBundle\Helper\UserInputDialog;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class BitBucketCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('bitbucket:create-repos')
            ->setDescription('BitBucket command to create repository')
            ->addArgument('name', InputArgument::OPTIONAL, 'Name of the project')
            ->addArgument('description', InputArgument::OPTIONAL, 'Description of the project');


    }


    private function loadBitbucketInfoFromConfig() {
        ConsoleConfig::$bitbucket_username = $this->getContainer()->getParameter('bb_username') ;
        ConsoleConfig::$bitbucket_password = $this->getContainer()->getParameter('bb_password') ;
        ConsoleConfig::$bitbucket_repos_account = $this->getContainer()->getParameter('bb_repos_account') ;
        ConsoleConfig::$bitbucket_private_policy =  $this->getContainer()->getParameter('bb_default_repos_privacy');

    }

    private function buildFieldForInputting($projectName, $projectDescription) {
        $fieldArray = array();
        if(empty($projectName)) {
            $fieldArray[] = array("title" => "Project name",
                "name" => "project_name",
                "required" => "yes");
        }
        if(empty($projectDescription)) {
            $fieldArray[] = array("title" => "Project description",
                "name" => "project_description",
                "required" => "yes");
        }
        if(empty(ConsoleConfig::$bitbucket_username)) {
            $fieldArray[] = array("title" => "Your bitbucket username",
                "name" => "bb_username",
                "required" => "yes");

        }
        if(empty(ConsoleConfig::$bitbucket_password)) {
            $fieldArray[] = array("title" => "Your bitbucket password",
                "name" => "bb_password",
                "required" => "yes");

        }
        if(empty(ConsoleConfig::$bitbucket_repos_account)) {
            $fieldArray[] = array("title" => "Owner account of the new repository",
                "name" => "repository_owner",
                "required" => "yes");

        }

        return $fieldArray;
    }



    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->loadBitbucketInfoFromConfig();
        $projectName = $input->getArgument('name');
        $projectDescription = $input->getArgument('description');
        $output->writeln("Starting Bitbucket project creation");
        $fields = $this->buildFieldForInputting($projectName, $projectDescription);
        if(!empty($fields)) {
            $dialog = $this->getHelperSet()->get('dialog');
            $output->writeln("Please input below info to create project on bitbucket");
            $inputDialog = new UserInputDialog($input, $output, $dialog);
            $params = $inputDialog->askForInput($fields);
            if(isset($params["project_name"])) {
                $projectName = $params["project_name"];
            }
            if(isset($params["project_description"])) {
                $projectDescription = $params["project_description"];
            }
            if(isset($params["bb_username"])) {
                ConsoleConfig::$bitbucket_username = $params["bb_username"];
            }
            if(isset($params["bb_password"])) {
                ConsoleConfig::$bitbucket_password = $params["bb_password"];
            }
            if(isset($params["repository_owner"])) {
                ConsoleConfig::$bitbucket_repos_account = $params["repository_owner"];
            }
        }

        $credential = new \Bitbucket\API\Authentication\Basic(ConsoleConfig::$bitbucket_username,
                                                            ConsoleConfig::$bitbucket_password);

        $repository = new \Bitbucket\API\Repositories\Repository();
        $repository->setCredentials($credential);
        $params = array(
            'scm'               => 'git',
            'is_private'        => true,
            'description'       => $projectDescription,
            'forking_policy'    => 'no_forks'
        );
        $message = $repository->create(ConsoleConfig::$bitbucket_repos_account, $projectName, $params);
        $output->writeln($message->getContent());


        $output->writeln("Finished Bitbucket project creation");
    }


}