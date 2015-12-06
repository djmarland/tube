<?php

namespace App\Controller;

use App\Presenter\MasterPresenter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Controller extends BaseController implements ControllerInterface
{
    /**
     * @var int
     */
    protected $currentPage = 1;

    /**
     * @var MasterPresenter
     */
    public $masterViewPresenter;

    /**
     * @var Request
     */
    public $request;

    /**
     * Setup common tasks for a controller
     * @param Request $request
     */
    public function initialize(Request $request)
    {
        $this->request = $request;
        $this->masterViewPresenter = new MasterPresenter();
        $this->toView('currentYear', date("Y"));
        $this->setSearchContext();
    }

    protected function getCurrentPage()
    {
        $page = $this->request->get('page', 1);

        // must be an integer string
        if (
            strval(intval($page)) !== strval($page) ||
            $page < 1
        ) {
            throw new HttpException(404, 'No such page value');
        }
        return (int)$page;
    }

    protected function setSearchContext()
    {
        $search = $this->request->get('q', null);
        $this->toView('searchContext', $search);
        $this->toView('searchAutofocus', null);
        $this->toView('showMasthead', true);
    }

    /**
     * @param int $total Total Results
     * @param int $currentPage The current page value
     * @param int $perPage How many per page
     */
    protected function setPagination(
        $total,
        $currentPage,
        $perPage
    ) {

        $pagination = new PaginationPresenter(
            $total,
            $currentPage,
            $perPage
        );

        if (!$pagination->isValid()) {
            throw new HttpException(404, 'There are not this many pages');
        }

        $this->toView('pagination', $pagination);
    }

    /**
     * Set values that make it to the view
     * @param $key
     * @param $value
     * @param bool  $inFeed
     * @return $this
     */
    public function toView(
        $key,
        $value,
        $inFeed = true
    ) {
        $this->masterViewPresenter->set($key, $value, $inFeed);
        return $this;
    }

    /**
     * @param $key
     * @return mixed
     * @throws \AppBundle\Domain\Exception\DataNotSetException
     */
    public function fromView($key)
    {
        return $this->masterViewPresenter->get($key);
    }

    protected function renderTemplate($template)
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

        $path = 'App:' . $template . '.' . $ext . '.twig';
        return $this->render($path, $this->masterViewPresenter->getData());
    }
}
