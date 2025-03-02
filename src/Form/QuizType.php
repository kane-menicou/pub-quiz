<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Quiz;
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
    public function __construct(#[Autowire('%kernel.project_dir%')] private readonly string $projectDir)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('friendlyId', HiddenType::class, [
                'data' => $this->generateRandomFriendlyId(),
            ])
            ->add('questionSet', ChoiceType::class, [
                'choices' => $this->allQuestionSets(),
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

    private function allQuestionSets(): array
    {
        $questionSets = (new Finder())->in($this->projectDir)->path('questions')->name(['*.yaml', '*.yml']);

        $names = [];
        foreach ($questionSets as $questionSet) {
            $names[$questionSet->getFilename()] = $questionSet->getRealPath();
        }

        return $names;
    }
}
