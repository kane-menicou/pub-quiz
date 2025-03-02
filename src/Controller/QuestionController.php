<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Quiz;
use App\Entity\QuizState;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Yaml\Yaml;

#[Route('/quizes/{quiz}/questions')]
class QuestionController extends AbstractController
{
    #[Route('/view')]
    public function view(#[MapEntity(mapping: ['quiz' => 'friendlyId'])] Quiz $quiz): Response
    {
        if ($quiz->getState() === QuizState::Lobby) {
            return $this->redirectToRoute('app_quiz_view', ['quiz' => $quiz->getFriendlyId()]);
        }

        return $this->render('question/view.html.twig', ['quiz' => $quiz]);
    }
}
