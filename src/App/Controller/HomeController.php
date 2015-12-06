<?php

namespace App\Controller;


class HomeController extends Controller
{
    public function indexAction()
    {
        $service = $this->get('app.services.line');
        $result = $service->findAll();
        $lines = $result->getDomainModels();

        $this->toView('lines', $lines);
        return $this->renderTemplate('home:index');
    }

    public function styleguideAction()
    {
        return $this->renderTemplate('home:styleguide');
    }
}
