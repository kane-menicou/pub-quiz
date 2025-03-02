<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Quiz;
use App\Entity\QuizState;
use App\Form\StartQuizType;
use App\Repository\QuizRepository;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/quizes/{quiz}/questions')]
class QuestionController extends AbstractController
{
    public function __construct(private readonly QuizRepository $quizRepository)
    {
    }

    #[Route('/view')]
    public function view(Request $request, #[MapEntity(mapping: ['quiz' => 'friendlyId'])] Quiz $quiz): Response
    {
        if ($quiz->getState() === QuizState::Lobby) {
            return $this->redirectToRoute('app_quiz_view', ['quiz' => $quiz->getFriendlyId()]);
        }

        $nextQuestion = $this->createForm(StartQuizType::class);
        $nextQuestion->handleRequest($request);

        if ($nextQuestion->isSubmitted() && $nextQuestion->isValid()) {
            $quiz->nextQuestion();

            $this->quizRepository->save($quiz);
        }

        return $this->render('question/view.html.twig', ['quiz' => $quiz, 'nextQuestion' => $nextQuestion->createView()]);
    }
}
