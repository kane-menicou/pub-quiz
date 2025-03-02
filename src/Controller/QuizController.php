<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Quiz;
use App\Entity\QuizState;
use App\Form\QuizType;
use App\Form\StartQuizType;
use App\Repository\QuestionRepository;
use App\Repository\QuizRepository;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use function count;

#[Route('/quizes')]
class QuizController extends AbstractController
{
    public function __construct(private readonly QuizRepository $repository, private readonly QuestionRepository $questionRepository)
    {
    }

    #[Route('/new')]
    public function new(Request $request): Response
    {
        $form = $this->createForm(QuizType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Quiz $quiz */
            $quiz = $form->getData();

            $this->repository->save($quiz);

            return $this->redirectToRoute('app_quiz_view', ['quiz' => $quiz->getFriendlyId()]);
        }

        return $this->render('quiz/new.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/{quiz}/view')]
    public function view(Request $request, #[MapEntity(mapping: ['quiz' => 'friendlyId'])] ?Quiz $quiz): Response
    {
        if ($quiz === null) {
            return $this->redirectToRoute('app_quiz_new');
        }

        if ($quiz->getState() === QuizState::Questions) {
            return $this->redirectToRoute('app_question_view', ['quiz' => $quiz->getFriendlyId()]);
        }

        $form = $this->createForm(StartQuizType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $quiz->nextQuestion();

            $this->repository->save($quiz);

            return $this->redirectToRoute('app_question_view', ['quiz' => $quiz->getFriendlyId()]);
        }

        return $this->render('quiz/view.html.twig', ['quiz' => $quiz, 'startForm' => $form->createView()]);
    }
}
