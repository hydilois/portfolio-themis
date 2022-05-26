<?php

namespace App\Controller\Admin;

use App\Entity\Project;
use App\Form\ProjectType;
use App\Repository\ProjectRepository;
use App\Repository\TagRepository;
use App\Security\Voter\ProjectVoter;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller used to manage portfolio contents in the backend.
 * @Route("/admin/project")
 * @IsGranted("ROLE_ADMIN")
 *
 * @author Hydil Aicard Sokeing <shydilaicard@gmail.com>
 */
class PortfolioController extends AbstractController
{
    /**
     * Lists all Project entities.
     *
     * @Route("/", methods="GET", name="admin_index")
     * @Route("/", methods="GET", name="admin_project_index")
     * @param ProjectRepository $projects
     * @return Response
     */
    public function index(ProjectRepository $projects): Response
    {
        $authorProjects = $projects->findBy(['author' => $this->getUser()], ['publishedAt' => 'DESC']);

        return $this->render('admin/portfolio/index.html.twig', ['projects' => $authorProjects]);
    }

    /**
     * Creates a new Project entity.
     *
     * @Route("/new", methods="GET|POST", name="admin_project_new")
     *
     * NOTE: the Method annotation is optional, but it's a recommended practice
     * to constraint the HTTP methods each controller responds to (by default
     * it responds to all methods).
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param FileUploader $fileUploader
     * @return Response
     */
    public function new(Request $request, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        $project = new Project();
        $project->setAuthor($this->getUser());
        $form = $this->createForm(ProjectType::class, $project)
            ->add('saveAndCreateNew', SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->saveFiles($form, $project, $fileUploader);
            $entityManager->persist($project);
            $entityManager->flush();
            $this->addFlash('success', "Le projet a été créé avec succès");
            if ($form->get('saveAndCreateNew')->isClicked()) {
                return $this->redirectToRoute('admin_project_new');
            }

            return $this->redirectToRoute('admin_project_index');
        }

        return $this->render('admin/portfolio/new.html.twig', [
            'project' => $project,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Finds and displays a Project entity.
     *
     * @Route("/{id<\d+>}", methods="GET", name="admin_project_show")
     * @param Project $project
     * @return Response
     */
    public function show(Project $project): Response
    {
        // This security check can also be performed
        // using an annotation: @IsGranted("show", subject="project", message="Projects can only be shown to their authors.")
        $this->denyAccessUnlessGranted(ProjectVoter::SHOW, $project, 'Les projets ne peuvent être mis à jour que par leur auteurs');

        return $this->render('admin/portfolio/show.html.twig', [
            'project' => $project,
        ]);
    }

    /**
     * Displays a form to edit an existing Project entity.
     *
     * @Route("/{id<\d+>}/edit", methods="GET|POST", name="admin_project_edit")
     * @IsGranted("edit", subject="project", message="Projects can only be edited by their authors.")
     * @param Request $request
     * @param Project $project
     * @param EntityManagerInterface $entityManager
     * @param FileUploader $fileUploader
     * @return Response
     */
    public function edit(Request $request, Project $project, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->saveFiles($form, $project,$fileUploader);
            $entityManager->flush();
            $this->addFlash('success', 'Le projet a été mis à jour avec succès');
            return $this->redirectToRoute('admin_project_edit', ['id' => $project->getId()]);
        }

        return $this->render('admin/portfolio/edit.html.twig', [
            'project' => $project,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Deletes a Project entity.
     *
     * @Route("/{id}/delete", methods="POST", name="admin_project_delete")
     * @IsGranted("delete", subject="project")
     * @param Request $request
     * @param Project $project
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function delete(Request $request, Project $project, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            return $this->redirectToRoute('admin_project_index');
        }
        $project->getTags()->clear();

        $entityManager->remove($project);
        $entityManager->flush();

        $this->addFlash('success', "Le projet a été supprimé avec succès");

        return $this->redirectToRoute('admin_project_index');
    }


    /**
     * @param FormInterface $form
     * @param Project $project
     * @param FileUploader $fileUploader
     */
    private function saveFiles(FormInterface $form, Project $project, FileUploader $fileUploader)
    {
        $guardImage = $form->get('guardImageName')->getData();
        $illustrationImage = $form->get('illustrativeImageName')->getData();

        if ($guardImage) {
            $guardImageName = $fileUploader->upload($guardImage);
            $project->setGuardImageName($guardImageName);
        }

        if ($illustrationImage) {
            $illustrationImageName = $fileUploader->upload($illustrationImage);
            $project->setIllustrativeImageName($illustrationImageName);
        }
    }

    /**
     * @Route("/", defaults={"page": "1", "_format"="html"}, methods="GET", name="blog_index")
     * @Route("/rss.xml", defaults={"page": "1", "_format"="xml"}, methods="GET", name="blog_rss")
     * @Route("/page/{page<[1-9]\d*>}", defaults={"_format"="html"}, methods="GET", name="blog_index_paginated")
     * @param Request $request
     * @param int $page
     * @param string $_format
     * @param ProjectRepository $projectRepository
     * @param TagRepository $tagRepository
     * @return Response
     */
    public function project(Request $request, int $page, string $_format, ProjectRepository $projectRepository, TagRepository $tagRepository): Response
    {
        $tag = null;
        if ($request->query->has('tag')) {
            $tag = $tagRepository->findOneBy(['name' => $request->query->get('tag')]);
        }
        $latestPosts = $projectRepository->findLatest($page, $tag);
        return $this->render('blog/index.'.$_format.'.twig', [
            'paginator' => $latestPosts,
            'tagName' => $tag ? $tag->getName() : null,
        ]);
    }
}
