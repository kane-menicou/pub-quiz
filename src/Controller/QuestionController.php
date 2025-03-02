<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Quiz;
use App\Form\StartQuizType;
use App\Repository\QuestionRepository;
use App\Repository\QuizRepository;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use function count;

#[Route('/quizes/{quiz}/questions')]
class QuestionController extends AbstractController
{
    public function __construct(private readonly QuizRepository $quizRepository, private readonly QuestionRepository $questionRepository)
    {
    }

    #[Route('/view')]
    public function view(Request $request, #[MapEntity(mapping: ['quiz' => 'friendlyId'])] Quiz $quiz): Response
    {
        if ($quiz->isInLobby() || $quiz->isComplete()) {
            return $this->redirectToRoute('app_quiz_view', ['quiz' => $quiz->getFriendlyId()]);
        }

        return $this->render('question/view.html.twig', ['quiz' => $quiz]);
    }
}
