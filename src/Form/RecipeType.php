<?php

namespace App\Form;

use App\Entity\Recipe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Event\PostSubmitEvent;
use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Sequentially;

class RecipeType extends AbstractType
{
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
                    new Sequentially([
                        new Length(min: 10, minMessage: 'Trop court.'),
                        new Regex(pattern: '/^[a-z0-9]+(?:-[a-z0-9]+)*$/', message: 'Format invalide.'),
                    ])
                ]
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Contenu',
                'empty_data' => '',
            ])
            ->add('duration')
            ->add('save', SubmitType::class,[
                'label' => 'Enregistrer',
            ])
            ->addEventListener(FormEvents::PRE_SUBMIT, $this->autoSlug(...))
            ->addEventListener(FormEvents::POST_SUBMIT, $this->attachTimestamps(...))
        ;
    }

    public function autoSlug(PreSubmitEvent $event): void
    {
        $data = $event->getData();

        if (empty($data['slug'])) {
            $slugger = new AsciiSlugger();
            $data['slug'] = strtolower($slugger->slug($data['title']));
        }
        $event->setData($data);
    }

    public function attachTimestamps(PostSubmitEvent $event): void
    {
        $data = $event->getData();

        if (!$data instanceof Recipe)
            return;

        $data->setUpdatedAt(new \DateTimeImmutable());
        if (!$data->getId())
            $data->setCreatedAt(new \DateTimeImmutable());
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
        ]);
    }
}
