<?php
namespace ConsoleBundle\Command;

use DateTime;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class StatusUpdateCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('tube:status:update')
            ->setDescription('Fetch data from TFL and populate the database');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Starting...');

        $output->writeln('Fetching data from TFL');
        $tflLines = $this->getContainer()->get('console.services.tfl')->fetchLineStatuses();
        if (!$tflLines) {
            $output->writeln('Error getting data');
            return 1;
        }

        // get the current lines and statuses
        $result = $this->getContainer()->get('console.services.line')->findAllWithStatus();
        if ($result->isEmpty()) {
            $output->writeln('Unable to fetch lines from database');
            return 1;
        }
        $lines = $result->getDomainModels();
        $updateService = $this->getContainer()->get('console.services.update');

        foreach ($lines as $line) {
            $output->writeln('---');
            $output->writeln('Checking ' . $line->getName());
            $tflLine = $this->getTFLLine($tflLines, $line->getTFLKey());

            $latestStatus = $line->getLatestStatus();
            $previousStatusTitle = null; // for first population
            $previousStatusDescriptions = null;

            if ($latestStatus) {
                $previousStatusTitle = $latestStatus->getTitle();
                $previousStatusDescriptions = $latestStatus->getDescriptions();
            }

            $currentStatusTitle = $tflLine->getStatusTitle();
            $currentStatusDescriptions = $tflLine->getStatusDescriptions();

            if (
                $previousStatusTitle == $currentStatusTitle &&
                $previousStatusDescriptions == $currentStatusDescriptions
            ) {
                $output->writeln('Status unchanged');
                continue;
            }

            $output->writeln('Status has changed. Saving new status');
            // update the statuses
            $updateService->addNewStatus($line, $tflLine);

            $output->writeln('Notifying subscribed users');
            // @todo - send a notification
        }

        $output->writeln('');
        $output->writeln('Done');
        return true;
    }

    private function getTFLLine($lines, $id)
    {
        foreach($lines as $line) {
            if ($line->getTFLKey() == $id) {
                return $line;
            }
        }
        throw new \Exception('Invalid Data');
    }
}
