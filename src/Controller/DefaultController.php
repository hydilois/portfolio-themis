<?php

namespace App\Controller;

use App\Entity\Project;
use App\Repository\ProjectRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
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
     * @param ProjectRepository $projectRepository
     * @return Response
     */
    public function projets(ProjectRepository $projectRepository): Response
    {
        return $this->render('projects/index.html.twig', [
            'active_link' => 'projects',
            'projects' => $projectRepository->findBy([], ['createdAt' => 'DESC'])
        ]);
    }

    /**
     * @Route("/projet/{slug}", name="project_detail")
     * @ParamConverter("project", options={"mapping": {"slug": "slug"}})
     * @param Project $project
     * @return Response
     */
    public function detailProject(Project $project): Response
    {
        return $this->render('projects/detail.html.twig', [
            'active_link' => 'projects',
            'project' => $project
        ]);
    }
}
