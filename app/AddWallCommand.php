<?php

namespace App;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Output\OutputInterface;

class AddWallCommand extends Command
{
    
    protected function configure()
    {
        $this
            ->setName('wall:add')
            ->setDescription('Add wall')
            ->addOption('wall_id', 'w', InputOption::VALUE_REQUIRED, 'Wall ID', false)
            ->addArgument('message', InputArgument::REQUIRED, 'Message')
        ;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        
        if (!$input->getOption('wall_id')) {
            $io->error('Missing Wall ID');
            exit(0);
        }
        
        $output->writeln('<info>Adding new wall</info>');
        
        //Создаем инстанс базы
        $db = new \MicroDB\Database('data/walls');
        
        if ($db->find(['wall_id' => $input->getOption('wall_id')])) {
            $io->error('This wall already in DB');
        } else {
            $db->create(['wall_id' => $input->getOption('wall_id'), 'message' => $input->getArgument('message')]);
        }
        
    }
    
}