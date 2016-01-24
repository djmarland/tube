<?php
namespace ConsoleBundle\Command;


use DateTime;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class StatusHourlyCommand extends Command
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
        $output->writeln('Getting disrupted lines...');
        $lines = $this->getContainer()->get('console.services.line')->findAllDisrupted();
        $count = $lines->getResultCount();
        $output->writeln($count . ' disrupted lines');
        $now = new \DateTimeImmutable();
        if ($count > 0) {
            $lines = $lines->getDomainModels();
            $day = (int) $now->format('N');
            $hour = (int) $now->format('H');
            foreach ($lines as $line) {
                $output->writeln(
                    'Getting relevant subscriptions for ' . $line->getName() . ' on day ' . $day . ', hour ' . $hour . '...'
                );
                $subscriptions = $this->getContainer()->get('console.services.subscription')
                    ->findAllForLineAndHour(
                        $line,
                        $day,
                        $hour
                    );
                $scount = $subscriptions->getResultCount();
                $output->writeln(
                    $scount . ' subscriptions found'
                );
                if ($scount > 0) {
                    $title = $line->getStatusSummary();
                    $this->notify(
                        $line,
                        $title,
                        $subscriptions->getDomainModels()
                    );
                    $output->writeln('Notified ' . $line->getName() . ': ' . $title);
                    // if we sent out some notifications, sleep for 2 seconds before sending more
                    sleep(2);
                }
            }
        }

        $output->writeln('Done');
    }
}