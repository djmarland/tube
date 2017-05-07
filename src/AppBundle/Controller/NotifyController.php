<?php

namespace AppBundle\Controller;

use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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

        // save the subscription data for this user
        $subscription = $request->get('subscription', null);

        if ($subscription) {
            $this->get('app.services.subscriptions')->addSubscriptionData($endpoint, $subscription);
        }

        // get the latest notification for this user
        $result = $this->get('app.services.notifcation')->findForEndpoint($endpoint);

        if ($result->getTitle() === 'DUMMY') {
            return new Response('<DUMMY>');
        }

        return new JsonResponse($result->getDomainModel());

    }

    public function subscribeAction(Request $request)
    {
        $endpoint = $request->get('endpoint');
        $key = $request->get('key', null);
        $line = $request->get('line');
        $times = $request->get('times');

        if (!$endpoint || !$line || !$times) {
            throw new HttpException(400, 'Missing data');
        }


        // temporarily disable firefox - as the encryption in php is not working
        if (strpos($endpoint, 'mozilla') !== false) {
            return new JsonResponse((object)[
                'status' => 'error',
                'message' => 'Sorry, Firefox is not yet supported'
            ]);
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

        try {
            $result = $this->get('app.services.subscriptions')->setForLine($line, $endpoint, $times, $key);
        } catch (Exception $e) {
            $result = false;
        }

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