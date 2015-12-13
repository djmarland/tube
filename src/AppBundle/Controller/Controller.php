<?php

namespace AppBundle\Controller;

use AppBundle\Presenter\MasterPresenter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Controller extends BaseController implements ControllerInterface
{
    /**
     * @var array
     */
    protected $appConfig;


    /**
     * @var MasterPresenter
     */
    protected $masterViewPresenter;

    /**
     * @var Request
     */
    protected $request;

    /**
     * Setup common tasks for a controller
     * @param Request $request
     */
    public function initialize(Request $request)
    {
        $this->request = $request;
        $this->appConfig = $this->getParameter('app.config');
        $this->masterViewPresenter = new MasterPresenter($this->appConfig);
    }

    public function toView(
        string $key,
        $value,
        $inFeed = true
    ): Controller {
        $this->masterViewPresenter->set($key, $value, $inFeed);
        return $this;
    }

    public function setTitle(string $title): Controller
    {
        $this->masterViewPresenter->setTitle($title);
        return $this;
    }

    public function fromView(string $key)
    {
        return $this->masterViewPresenter->get($key);
    }

    protected function renderTemplate(string $template)
    {
        $format = $this->request->get('format', null);
        if ($format == 'json') {
            return new JsonResponse($this->masterViewPresenter->getFeedData());
        }

        $ext = 'html';
        if (in_array($format, ['inc'])) {
            $ext = $format;
        } elseif ($format) {
            throw new HttpException(404, 'Invalid Format');
        }

        $path = 'AppBundle:' . $template . '.' . $ext . '.twig';
        return $this->render($path, $this->masterViewPresenter->getData());
    }
}
