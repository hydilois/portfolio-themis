<?php


namespace App\Form;

use App\Entity\Project;
use App\Form\Type\TagsInputType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Constraints\Image;

/**
 * Defines the form used to create and manipulate blog posts.
 *
 */
class ProjectType extends AbstractType
{
    private $slugger;

    // Form types are services, so you can inject other services in them if needed
    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'Titre du projet',
            ])
            ->add('website', TextType::class, [
                'label' => 'Lien du projet',
                'required' => false
            ])
            ->add('summary', TextareaType::class, [
                'help' => 'Les résumés ne peuvent pas contenir de contenu Markdown ou HTML ; seulement du texte brut.',
                'label' => 'Résumé',
            ])
            ->add('guardImageName', FileType::class, [
                'label' => "Image de garde du projet",
                'help' => "Dimensions(370px x 370px)",
                'required' => false,
                'mapped' => false,
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new Image([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/jpg',
                        ]
                    ])
                ]
            ])
            ->add('illustrativeImageName', FileType::class, [
                'label' => "Image Illustrative",
                'help' => "Dimensions(405px x 670px)",
                'required' => false,
                'mapped' => false,
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new Image([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/jpg',
                        ]
                    ])
                ]
            ])
            ->add('content', null, [
                'attr' => ['rows' => 20],
                'help' => "Utilisez du Markdown pour formater le contenu de l'article. Le HTML est également autorisé.",
                'label' => 'Contenu',
            ])
            ->add('tags', TagsInputType::class, [
                'label' => 'Catégorie',
                'required' => false,
            ])
            ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
                /** @var Project */
                $post = $event->getData();
                if (null !== $postTitle = $post->getTitle()) {
                    $post->setSlug($this->slugger->slug($postTitle)->lower());
                }
            });
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
        ]);
    }

    private function vichOptions($minHeight, $maxHeight, $minWidth, $maxWidth)
    {
        return [
            new Image([
                'maxSize' => '2M',
                'minHeight' => $minHeight,
                'minHeightMessage' => "L'image doit faire exactement " . $minHeight . "px",
                'maxHeight' => $maxHeight,
                'maxHeightMessage' => "L'image doit faire exactement " . $maxHeight . "px",
                'minWidth' => $minWidth,
                'maxWidth' => $maxWidth,
                'mimeTypes' => [
                    'image/jpeg',
                    'image/png',
                    'image/jpg',
                ]
            ])
        ];
    }
}
