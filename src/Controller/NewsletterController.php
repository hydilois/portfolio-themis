<?php


namespace App\Controller;


use App\Entity\Newsletter;
use App\Repository\NewsletterRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class NewsletterController extends AbstractController
{
    /**
     * @var NewsletterRepository
     */
    private NewsletterRepository $newsletterRepository;
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $manager;

    /**
     * NewsletterController constructor.
     * @param NewsletterRepository $newsletterRepository
     * @param EntityManagerInterface $manager
     */
    public function __construct(NewsletterRepository $newsletterRepository, EntityManagerInterface $manager)
    {
        $this->newsletterRepository = $newsletterRepository;
        $this->manager = $manager;
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     * @Route("/subscribe/newsletter", name="subscribe_newsletter", methods={"POST"})
     */
    public function subscribeNewsletter(Request $request)
    {
        $email = $request->request->get('email');
        $existingEmail = $this->newsletterRepository->findOneBy(['email' => $email]);
        if ($existingEmail) {
            $this->addFlash('success', "Votre souscription à la newsletter a été enregistré avec succès.");
            return $this->redirectToRoute('homepage');
        }
        $newsletter = new  Newsletter();
        $newsletter->setEmail($email);
        $this->manager->persist($newsletter);
        $this->manager->flush();
        $this->addFlash('success', "Votre souscription à la newsletter a été enregistré avec succès.");
        return $this->redirectToRoute('homepage');
    }
}