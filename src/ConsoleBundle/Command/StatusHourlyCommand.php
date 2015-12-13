<?php
namespace ConsoleBundle\Command;


use DateTime;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class StatusHourlyCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('tube:status:hourly')
            ->setDescription('Finds users who are subscribed for this hour');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Starting...');
        $output->writeln('Done (nothing to do yet)');
    }
}