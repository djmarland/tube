<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class MainController extends Controller
{
    public function initialize(Request $request)
    {
        parent::initialize($request);
        $this->toView('lines', $this->getLines());
    }

    public function indexAction()
    {
        return $this->renderTemplate('main:index');
    }

    public function settingsAction()
    {
        return $this->renderTemplate('main:settings');
    }

    public function lineAction(Request $request)
    {
        $lineKey = $request->get('line');
        $allLines = $this->fromView('lines');
        $line = null;
        foreach ($allLines as $l) {
            if ($l->getKey() == $lineKey) {
                $line = $l;
            }
        }
        if (!$line) {
            throw new HttpException(404, 'Line ' . $lineKey . ' does not exist.');
        }

        $this->toView('line', $line);

        return $this->renderTemplate('main:line');
    }

    public function styleguideAction()
    {
        return $this->renderTemplate('main:styleguide');
    }

    private function getLines()
    {
        $lines = [];
        $lineData = [
            ['bakerloo', 'Bakerloo', ['Good Service']],
            ['central', 'Central', ['Good Service']],
            ['circle', 'Circle', ['Part Suspended', 'Minor Delays']],
            ['district', 'District', ['Good Service']],
            ['hammersmith-city', 'Hammersmith & City', ['Good Service']],
            ['jubilee', 'Jubilee', ['Severe Delays']],
            ['metropolitan', 'Metropolitan', ['Good Service']],
            ['northern', 'Northern', ['Good Service']],
            ['piccadilly', 'Piccadilly', ['Good Service']],
            ['victoria', 'Victoria', ['Good Service']],
            ['waterloo-city', 'Waterloo & City', ['Planned Closure']],
            ['dlr', 'DLR', ['Good Service']],
            ['london-overground', 'London Overground', ['Part Closure']],
            ['tfl-rail', 'TFL Rail', ['Severe Delays']],
        ];

        foreach ($lineData as $data) {
            $lines[] = new \TubeService\Domain\Entity\Line(
                new \TubeService\Domain\ValueObject\ID(0),
                new \DateTime(),
                new \DateTime(),
                $data[0],
                $data[1],
                $data[2]
            );
        }
        return $lines;
    }
}
