<?php
namespace ConsoleBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use TubeService\Domain\Entity\Line;

abstract class Command extends ContainerAwareCommand
{
    protected function notify(
        Line $line,
        $title,
        $subscriptions
    ) {
        $notificationService = $this->getContainer()->get('console.services.notification');

        $config = $this->getContainer()->getParameter('app.config');
        foreach($subscriptions as $subscription) {
            // create a notification for everyone
            $notificationService->createNew(
                $subscription->getEndpoint(),
                $line->getName(),
                $title,
                '/' . $line->getURLKey(),
                '/static/icons/' . $config['asset_version'] . '/icon-' . $line->getURLKey() . '.png'
            );
        }

        $pushService = $this->getContainer()->get('console.services.push');
        $pushService->notifyChromeUsers(array_filter($subscriptions, function($sub) {
            return $sub->isChrome();
        }));
        $pushService->notifyFirefoxUsers(array_filter($subscriptions, function($sub) {
            return $sub->isFirefox();
        }));

        return true;
    }
}