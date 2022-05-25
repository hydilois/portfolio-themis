<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\Tag;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class UsefulController extends AbstractController
{

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $manager;
    /**
     * @var SluggerInterface
     */
    private SluggerInterface $slugger;

    public function __construct(EntityManagerInterface $manager, SluggerInterface $slugger)
    {
        $this->manager = $manager;
        $this->slugger = $slugger;
    }

    /**
     * @Route("/admin/userful", name="app_userful")
     */
    public function index(): Response
    {

        $projets = $this->manager->getRepository(Project::class)->findAll();
        if (count($projets) > 10) {
            return $this->redirectToRoute('admin_project_index');
        }
        $faker = Factory::create('fr_FR');
        $tagRepository = $this->manager->getRepository(Tag::class);
        foreach ($this->loadTags() as $tagName) {
            $existingTag = $tagRepository->findOneBy(['name' => $tagName]);
            if ($existingTag === NULL) {
                $tag = new Tag();
                $tag->setName($tagName);
                $this->manager->persist($tag);
                $this->manager->flush();
            }
        }
        $tags = $tagRepository->findAll();
        $user = $this->manager->getRepository(User::class)->findOneBy(['email' => 'admin@themis-it.com']);
        for ($j = 0; $j < 10; $j++) {
            $projet = new Project();
            $projet->setAuthor($user);
            $projet->setTitle($faker->company());
            $projet->setSlug($this->slugger->slug($projet->getTitle()));
            $projet->setSummary($faker->paragraph(3));
            $projet->setContent($faker->paragraph(100));
            $projet->setGuardImageName('Webp-net-resizeimage-628ce97e2dfc3.png');
            $projet->setIllustrativeImageName('Webp-net-resizeimage-1-628ce97e2f9cc.png');
            $num = rand(1, count($tags));
            $rand_keys = array_rand($tags, $num);
            if (is_array($rand_keys)) {
                for ($i = 0; $i < count($rand_keys); $i++) {
                    $projet->addTag($tags[$rand_keys[$i]]);
                }
            } else {
                $projet->addTag($tags[$rand_keys]);
            }
            $this->manager->persist($projet);
        }
        $this->manager->flush();
        return $this->redirectToRoute('admin_project_index');
    }

    /**
     * @return string[]
     */
    private function loadTags()
    {
        return ["Design", "Infographie", "Mobile", "App Dev", "Sécurité", "Immobilier", "Business", "Formation", "E-learning"];
    }
}
