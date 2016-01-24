<?php

namespace AppBundle\Controller;

use ConsoleBundle\Command\StatusHourlyCommand;
use ConsoleBundle\Command\StatusUpdateCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class UpdateController extends Controller
{

    public function minuteAction(Request $request)
    {
        //Define your file path based on the cache one
        $filename = $this->container->getParameter('kernel.cache_dir') . '/timestamps/minute.txt';
        $data = @file_get_contents($filename);
        $now = time();
        $last = (int) $data ?? 0;
        $diff = $now - $last;

        if ($diff > 50) {
            $command = new StatusUpdateCommand();
            $command->setContainer($this->container);
            $input = new ArrayInput([]);
            $output = new NullOutput();

            $resultCode = $command->run($input, $output);

            //Create your own folder in the cache directory
            $fs = new Filesystem();
            $fs->mkdir(dirname($filename));

            file_put_contents($filename, (string) $now);
        } else {
            $resultCode = 1;
        }

        return new JsonResponse((object) [
            'status' => $resultCode,
            'diff' => $diff
        ]);
    }

    public function hourAction()
    {
        //Define your file path based on the cache one
        $filename = $this->container->getParameter('kernel.cache_dir') . '/timestamps/hour.txt';
        $data = @file_get_contents($filename);
        $now = new \DateTimeImmutable();
        $min = (int) $now->format('i');
        $timestamp = $now->getTimestamp();
        $last = (int) $data ?? 0;
        $diff = $timestamp - $last;

        // can only run in the first 5 mins of an hour,
        // and cannot run again within 10 mins
        $withinMin = ($min <= 5);
        if ($withinMin && $diff > (10*60)) {
            $command = new StatusHourlyCommand();
            $command->setContainer($this->container);
            $input = new ArrayInput([]);
            $output = new NullOutput();

            $resultCode = $command->run($input, $output);

            //Create your own folder in the cache directory
            $fs = new Filesystem();
            $fs->mkdir(dirname($filename));

            file_put_contents($filename, (string) $now);
        } else {
            $resultCode = 1;
        }

        return new JsonResponse((object) [
            'status' => $resultCode,
            'diff' => $diff,
            'inWindow' => $withinMin
        ]);
    }
}
