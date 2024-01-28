<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Log\Events\MessageLogged;
use Illuminate\Queue\InteractsWithQueue;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

class MessageLoggedListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  MessageLogged  $event
     * @return void
     */
    public function handle(MessageLogged $event)
    {
        
        if (app()->runningInConsole()) {
            $output = new ConsoleOutput();
            switch($event->level){
                case ("error"):
                    $output->writeln("<error>{$event->message}</error>");
                break;
                case "warning":
                    $output->writeln("<comment>{$event->message}</comment>");
                break;
                case "debug":
                    $outputStyle = new OutputFormatterStyle('white', 'black',[]);
                    $output->getFormatter()->setStyle('notice', $outputStyle);
                    $output->writeln("<notice>{$event->message}</notice>");
                break;
                case "info":
                    $output->writeln("<info>{$event->message}</info>");
                break;
                case "critical":
                    $outputStyle = new OutputFormatterStyle('white', 'red', ['bold', 'blink']);
                    $output->getFormatter()->setStyle('critical', $outputStyle);
                    $output->writeln("<critical>{$event->message}</critical>");
                break;
                case "notice":
                    $output->writeln("<question>{$event->message}</question>");
                break;
                case "alert":
                    $outputStyle = new OutputFormatterStyle('red', 'yellow', ['bold', 'blink']);
                    $output->getFormatter()->setStyle('alert', $outputStyle);
                    $output->writeln("<alert>{$event->message}</alert>");
                break;

            }
            //$output->writeln("<error>{$event->message}</error>");
        }

// $outputStyle = new OutputFormatterStyle('red', 'yellow', ['bold', 'blink']);
// $output->getFormatter()->setStyle('fire', $outputStyle);
// // green text
// $output->writeln('<info>foo</info>');

// // yellow text
// $output->writeln('<comment>foo</comment>');

// // black text on a cyan background
// $output->writeln('<question>foo</question>');

// // white text on a red background
// $output->writeln('<error>foo</error>');
    }
}
