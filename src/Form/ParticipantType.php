<?php

namespace App\Form;

use App\Entity\Participant;
use App\Entity\Quiz;
use App\Repository\QuizRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ParticipantType extends AbstractType
{
    public function __construct(private readonly QuizRepository $quizRepository)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'quiz',
                TextType::class,
                [
                    'constraints' => [new NotBlank(message: 'Code incorrect')],
                    'label' => 'Quiz Code',
                ],
            )
            ->add('name')
        ;

        $builder->get('quiz')->addViewTransformer(
            new CallbackTransformer(
                fn (?Quiz $quiz): ?string => $quiz?->getFriendlyId(),
                fn (mixed $friendlyId): ?Quiz => $this->quizRepository->findOneByFriendlyId($friendlyId),
            ),
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
