<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class NotifyController extends Controller
{
    public function subscribeAction(Request $request)
    {
        $endpoint = $request->get('endpoint');
        $lines = $request->get('lines');

        if (!$endpoint || !$lines) {
            throw new HttpException(400, 'Missing data');
        }

        // @todo - identify this user uniquely from the endpoint

        // @todo - see if this user and line already has an entry

        // @todo - update/create the entry for this line

        // @todo - return appropriate response
        return new JsonResponse((object) [
            'status' => 'ok'
        ]);
    }

    public function unsubscribeAction()
    {

        throw new HttpException(404, 'Not yet');
    }
}