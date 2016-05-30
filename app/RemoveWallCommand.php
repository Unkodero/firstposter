<?php

namespace App;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Output\OutputInterface;

class RemoveWallCommand extends Command
{
    
    protected function configure()
    {
        $this
            ->setName('wall:remove')
            ->setDescription('Remove wall')
            ->addOption('wall_id', 'w', InputOption::VALUE_REQUIRED, 'Wall ID', false)
        ;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        
        if (!$input->getOption('wall_id')) {
            $io->error('Missing Wall ID');
            exit(0);
        }
       
        //Создаем инстанс базы
        $db = new \MicroDB\Database('data/walls');
        $wall = $db->find(['wall_id' => $input->getOption('wall_id')]);

        if (!$wall) {
            $io->error('Could not find wall with this ID. Use "wall:list" to see list of aviabile walls');
        } else {
            foreach ($wall as $id => $value) {
                $db->delete($id);
            }
        }
        
    }
    
}