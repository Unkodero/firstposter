<?php

namespace App;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Output\OutputInterface;

class WallListCommand extends Command
{
    
    protected function configure()
    {
        $this
            ->setName('wall:list')
            ->setDescription('Table of walls in DB')
        ;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        
        //Создаем инстанс базы
        $db = new \MicroDB\Database('data/walls');
        
        $io->table(
            ['Wall ID', 'Message'],
            $db->find()
        );        
        
    }
    
}