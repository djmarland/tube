<?php

namespace AppBundle\Controller;

use ConsoleBundle\Command\StatusUpdateCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class UpdateController extends Controller
{

    public function minuteAction(Request $request)
    {
        $command = new StatusUpdateCommand();
        $command->setContainer($this->container);
        $input = new ArrayInput([]));
        $output = new NullOutput();

        $resultCode = $command->run($input, $output);

        return new Response($resultCode);
    }
}