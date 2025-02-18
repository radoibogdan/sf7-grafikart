<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Recipe;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Sequentially;

class RecipeType extends AbstractType
{

    public function __construct(private FormListenerFactory $formListenerFactory)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                // Mettre une chaine vide à la place de NULL si aucune valeur n'est renseignée dans le champ title
                'empty_data' => '',
            ])
            ->add('slug', TextType::class, [
                'required' => false,
                'constraints' => [
                    // Si la 1ere contrainte est Invalide il s'arrête.
//                    new Sequentially([
//                        new Length(min: 10, minMessage: 'Trop court.'),
//                        new Regex(pattern: '/^[a-z0-9]+(?:-[a-z0-9]+)*$/', message: 'Format invalide.'),
//                    ])
                ]
            ])
            ->add('category', CategoryAutocompleteField::class)
            ->add('content', TextareaType::class, [
                'label' => 'Contenu',
                'empty_data' => '',
            ])
            ->add('thumbnailFile', FileType::class, [
                'required' => false,
            ])
            ->add('duration')
            ->add('quantities', CollectionType::class, [
                'entry_type' => QuantityType::class,
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'entry_options' => ['label' => false],
                'attr' => [
//                    'data-controller' => 'form-collection', // rajouté automatiquement dans le extend_bootstrap.html.twig
                    'data-form-collection-add-label-value' => 'Ajouter un ingrédient',
                    'data-form-collection-delete-label-value' => 'Supprimer un ingrédient',
                    'class' => 'd-flex',
                ]
            ])
            ->add('save', SubmitType::class,[
                'label' => 'Enregistrer',
            ])
            // Var 1 : Create listener with method inside
//            ->addEventListener(FormEvents::PRE_SUBMIT, $this->autoSlug(...))
            // Var 2 : Create service with method inside service
            ->addEventListener(FormEvents::PRE_SUBMIT, $this->formListenerFactory->autoSlug('title'))
            ->addEventListener(FormEvents::POST_SUBMIT, $this->formListenerFactory->timestamps())
        ;
    }

      // Var 1 : Create listener with method inside
//    public function autoSlug(PreSubmitEvent $event): void
//    {
//        $data = $event->getData();
//
//        if (empty($data['slug'])) {
//            $slugger = new AsciiSlugger();
//            $data['slug'] = strtolower($slugger->slug($data['title']));
//        }
//        $event->setData($data);
//    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
        ]);
    }
}
