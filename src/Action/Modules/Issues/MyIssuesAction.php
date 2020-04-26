<?php

namespace App\Action\Modules\Issues;

use App\Controller\Core\AjaxResponse;
use App\Controller\Core\Application;
use App\Controller\Core\Controllers;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MyIssuesAction extends AbstractController
{

    const TWIG_TEMPLATE_PENDING_ISSUES = 'modules/my-issues/ongoing.twig';

    /**
     * @var Application $app
     */
    private $app;

    /**
     * @var Controllers $controllers
     */
    private $controllers;

    public function __construct(Application $app, Controllers $controllers) {
        $this->app         = $app;
        $this->controllers = $controllers;

    }

    /**
     * @Route("/my-issues/pending", name="my-issues-pending")
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function displayPendingIssues(Request $request) {

        if (!$request->isXmlHttpRequest()) {
            return $this->renderTemplate( false);
        }
        $template_content  = $this->renderTemplate( true)->getContent();
        return AjaxResponse::buildResponseForAjaxCall(200, "", $template_content);
    }

    /**
     * @param bool $ajax_render
     * @return Response
     */
    private function renderTemplate($ajax_render = false)
    {

        $all_ongoing_issues = $this->app->repositories->myIssueRepository->findAllNotDeletedAndNotResolved();
        $issues_cards_dtos  = $this->controllers->getMyIssuesController()->buildIssuesCardsDtosFromIssues($all_ongoing_issues);

        $data = [
            'ajax_render'       => $ajax_render,
            'issues_cards_dtos' => $issues_cards_dtos
        ];

        return $this->render(self::TWIG_TEMPLATE_PENDING_ISSUES, $data);
    }

}