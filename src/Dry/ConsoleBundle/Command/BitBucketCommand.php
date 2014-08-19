<?php

namespace Dry\ConsoleBundle\Command;
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
            ->addArgument('name', InputArgument::REQUIRED, 'Name of the project')
            ->addArgument('description', InputArgument::REQUIRED, 'Description of the project')
            ->addArgument('private', InputArgument::OPTIONAL, 'yes (default)/no - yes if the project is private. ');

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $projectName = $input->getArgument('name');
        $projectDescription = $input->getArgument('description');
        $private = $input->getArgument('private');
        $bbUser = $this->getContainer()->getParameter('bb_username') ;
        $bbPassword = $this->getContainer()->getParameter('bb_password') ;
        $bbReposAccount = $this->getContainer()->getParameter('bb_repos_account') ;
        $bbDefaultReposPrivacy = $this->getContainer()->getParameter('bb_default_repos_privacy') ;
        if(!empty($private)) {
            $isPrivate = ($private == 'yes');
        } else {
            $isPrivate = ($bbDefaultReposPrivacy == 'private');
        }

        $credential = new \Bitbucket\API\Authentication\Basic($bbUser, $bbPassword);

        $repository = new \Bitbucket\API\Repositories\Repository();
        $repository->setCredentials($credential);
        $params = array(
            'scm'               => 'git',
            'is_private'        => $isPrivate,
            'description'       => $projectDescription,
            'forking_policy'    => 'no_forks'
        );
        $repository->create($bbReposAccount, $projectName, $params);

        $output->writeln("Finished Bitbucket project creation");
    }


}