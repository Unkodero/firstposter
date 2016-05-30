<?php

namespace App;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Output\OutputInterface;

class PosteCommand extends Command
{
    
    protected function configure()
    {
        $this
            ->setName('poste')
            ->setDescription('Poste loop')          
            ->addArgument('access_token', InputArgument::REQUIRED, 'Access Token')
            ->addOption('delay', 'd', InputOption::VALUE_OPTIONAL, 'Delay', 3)  
        ;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        
        $io->text([
            '=========================',
            '=      FirstPoster      =',
            '=       Unkodero        =',
            '=========================',
        ]);   
        
        //Проверяем токен
        try {
            $vk = \getjump\Vk\Core::getInstance()->apiVersion('5.5')->setToken($input->getArgument('access_token'));
            $vk->request('users.get')->each(function() {});          
        } catch (Vk\Exception\Error $e) {
            $io->error($e->getMessage());
            exit(0);
        }  
        
        //Создаем инстанс базы
        $db = new \MicroDB\Database('data/walls');
        $walls = $db->find();
        
        $loop = \React\EventLoop\Factory::create();
        
        //Основной цикл
        $loop->addPeriodicTimer($input->getOption('delay'), function () use (&$vk, &$io, &$walls) {
            //А есть ли с чем работать?
            if (count($walls) == 0) {
                $io->error('No another walls');
                exit(0);
            }
            
            //Обрабатываем каждую стену
            foreach ($walls as $id => $wall) {
                $lastPost = false;
                
                try {
                    //Получаем 2 последних поста со стены. 2 - потому что первый может быть закреплен
                    $vk->request('wall.get', [
                        'owner_id' => $wall['wall_id'],
                        'count' => 2
                    ])->each(function($i, $v) use (&$lastPost) { 
                        //Если не закреплен и не установлен последний пост, а то вдруг закрепленного поста нет?
                        if (!isset($v->is_pinned) && !$lastPost) {
                            $lastPost = $v->id;
                        }
                    });
                    
                    //Если до этого последний пост не присвоен - значит, это первый цикл, ловить тут нечего
                    if (!isset($wall['last_post'])) {
                        $walls[$id]['last_post'] = $lastPost;
                        break;
                    //Значит, не первый
                    } elseif ($wall['last_post'] !== $lastPost) {
                        
                        $walls[$id]['last_post'] = $lastPost;
                        
                        try {
                            //Отправляем коммент
                            $vk->request('wall.addComment', [
                                'owner_id' => $wall['wall_id'],
                                'post_id' => $lastPost,
                                'text' => $wall['message']
                            ])->each(function() {});
                            
                            //Первыйнах
                            $io->success('Success added comment to post https://vk.com/wall'.$wall['wall_id'].'_'.$lastPost);
                        } catch (\getjump\Vk\Exception\Error $e) {
                            //А вдруг забанили? Потом сделаю нормальную проверку
                            $io->error($e->getMessage());
                            unset($walls[$id]);
                        }
                    
                    }
                
                } catch (\getjump\Vk\Exception\Error $e) {
                    //Выше сказано
                    $io->error($e->getMessage());
                    unset($walls[$id]);
                }
                
            }
        });        

        //Каждую минуту говорим, что живы. Теплый - не значит живой (с) Надя
        $loop->addPeriodicTimer(60, function () use (&$io) {
            $io->note('I am alive');            
        });
        
        //Каждые 5 минут выводим системную информацию
        $loop->addPeriodicTimer(360, function () use (&$io, &$walls) {
            //Сколько памяти используем, ибо нехуй.
            $memory = memory_get_usage() / 1024;
            $formatted = number_format($memory, 3).'K';    
            
            $io->section('==== Current status ===');
            $io->text([
                'Memory usage: '.$formatted,
                'Walls in loop:'
            ]);
            
            //Кто в работе
            $io->table(
                ['Wall ID', 'Message'],
                $walls
            );              
        });       
        
        //START AUCHTUNG MACHINE!!!1!!!!
        $loop->run();
        
    }
    
}