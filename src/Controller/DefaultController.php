<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'active_link' => 'home'
        ]);
    }

    /**
     * @Route("/liste-des-projets", name="projects_list")
     */
    public function projets(): Response
    {
        return $this->render('projects/index.html.twig', [
            'active_link' => 'projects'
        ]);
    }

    /**
     * @Route("/projet/slug", name="project_detail")
     */
    public function detailProject(): Response
    {
        return $this->render('projects/detail.html.twig', [
            'active_link' => 'projects'
        ]);
    }
}
