<?php

namespace AppBundle\Controller;

use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class NotifyController extends Controller
{
    public function statusAction(Request $request)
    {
        $endpoint = $request->get('endpoint');
        if (!$endpoint) {
            throw new HttpException(400, 'Missing data');
        }

        $result = $this->get('app.services.subscriptions')->findAllForEndpoint($endpoint);

        return new JsonResponse($result->getDomainModels());
    }

    public function latestAction(Request $request)
    {
        $endpoint = $request->get('endpoint');
        if (!$endpoint) {
            throw new HttpException(400, 'Missing data');
        }

        // get the latest notification for this user
        $result = $this->get('app.services.notifcation')->findForEndpoint($endpoint);

        return new JsonResponse($result->getDomainModel());

    }

    public function subscribeAction(Request $request)
    {
        $endpoint = $request->get('endpoint');
        $line = $request->get('line');
        $times = $request->get('times');

        if (!$endpoint || !$line || !$times) {
            throw new HttpException(400, 'Missing data');
        }

        $line = $this->get('app.services.line')->findByKey($line);
        if (!$line) {
            throw new HttpException(400, 'Invalid Line data');
        }

        try {
            $times = json_decode($times);
        } catch (Exception $e) {
            throw new HttpException(400, 'Invalid Time data');
        }

        $times = $this->groupTimes($times);


        $result = $this->get('app.services.subscriptions')->setForLine($line, $endpoint, $times);


        // @todo - return appropriate response
        return new JsonResponse((object) [
            'status' => $result ? 'ok' : 'error'
        ]);
    }

    public function unsubscribeAction(Request $request)
    {
        $endpoint = $request->get('endpoint');

        if (!$endpoint) {
            throw new HttpException(400, 'Missing data');
        }

        $result = $this->get('app.services.subscriptions')->removeForEndpoint($endpoint);


        // @todo - return appropriate response
        return new JsonResponse((object) [
            'status' => $result ? 'ok' : 'error'
        ]);
    }

    private function groupTimes($times): array
    {
        $days = [];
        foreach ($times as $time) {
            $time = explode('-', $time);
            $day = $time[0];
            $hour = $time[1];
            if (!isset($days[$day])) {
                $days[$day] = [];
            }
            $days[$day][] = $hour;
            sort($days[$day]);
        }

        foreach ($days as $i => $day) {
            $groups = [];
            $currentGroup = null;
            foreach ($day as $hour) {
                if (!$currentGroup || $currentGroup->end != ($hour - 1)) {
                    if ($currentGroup) {
                        $groups[] = $currentGroup;
                    }
                    // new group
                    $currentGroup = (object) [
                        'start' => $hour
                    ];
                }
                $currentGroup->end = $hour;
            }
            if ($currentGroup) {
                $groups[] = $currentGroup;
            }
            $days[$i] = $groups;
        }
        return $days;
    }
}