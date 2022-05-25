<?php

namespace App\Controller;

use App\Entity\Messages;
use App\Entity\Project;
use App\Form\MessagesType;
use App\Repository\ProjectRepository;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/", name="homepage")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function index(Request $request, EntityManagerInterface $manager): Response
    {
        $message = new Messages();
        $form = $this->createForm(MessagesType::class, $message);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($message);
            $manager->flush();
            $this->addFlash('success', "Votre message a été envoyé avec succès.");
        }
        $this->addFlash('success', "Votre message a été envoyé avec succès.");

        return $this->render('home/index.html.twig', [
            'active_link' => 'home',
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/liste-des-projets", name="projects_list")
     * @param ProjectRepository $projectRepository
     * @param TagRepository $tagRepository
     * @return Response
     */
    public function projets(ProjectRepository $projectRepository, TagRepository $tagRepository): Response
    {
        return $this->render('projects/index.html.twig', [
            'active_link' => 'projects',
            'projects' => $projectRepository->findBy([], ['createdAt' => 'DESC']),
            'tags' => $tagRepository->findAll()
        ]);
    }

    /**
     * @Route("/projet/{slug}", name="project_detail")
     * @ParamConverter("project", options={"mapping": {"slug": "slug"}})
     * @param Project $project
     * @param ProjectRepository $projectRepository
     * @return Response
     */
    public function detailProject(Project $project, ProjectRepository $projectRepository): Response
    {
        $currentId = $project->getId();
        return $this->render('projects/detail.html.twig', [
            'active_link' => 'projects',
            'project' => $project,
            'previous_project' => ($currentId - 1) !== 0 ? $projectRepository->find($currentId - 1) : null,
            'next_project' => $projectRepository->find($currentId + 1)
        ]);
    }
}
