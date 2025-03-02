<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Quiz;
use App\Repository\QuestionRepository;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use function array_merge;
use function count;
use function random_int;
use function range;

class QuizType extends AbstractType
{
    public function __construct(private readonly QuestionRepository $questionRepository)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('friendlyId', HiddenType::class, [
                'data' => $this->generateRandomFriendlyId(),
            ])
            ->add('questionSet', ChoiceType::class, [
                'choices' => $this->questionRepository->listAllQuestionSets(),
            ])
            ->add('secondsPerQuestion', ChoiceType::class, [
                'choices' => [
                    '20 Seconds (Recommended)' => 20,
                    '5 Seconds' => 10,
                    '10 Seconds' => 10,
                    '30 Seconds' => 30,
                    '1 Minute' => 60,
                    '5 Minutes' => 300,
                    '59 Minutes' => 3_540,
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Quiz::class,
        ]);
    }

    private function generateRandomFriendlyId(): string
    {
        $chars = array_merge(range('A', 'Z'), range('0', '9'));

        $output = [];
        foreach (range(1, Quiz::FRIENDLY_ID_LENGTH) as $index) {
            $output[] = $chars[random_int(0, count($chars) - 1)];
        }

        return implode('', $output);
    }
}
