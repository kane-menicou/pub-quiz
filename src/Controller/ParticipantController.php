<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\Quiz;
use App\Form\ParticipantType;
use App\Form\QuizType;
use App\Repository\ParticipantRepository;
use App\Repository\QuizRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;
use function var_dump;

#[Route('/participants')]
class ParticipantController extends AbstractController
{
    #[Route('/new')]
    public function new(Request $request, ParticipantRepository $repository, #[MapQueryParameter] ?string $friendlyId, QuizRepository $quizRepository): Response
    {
        $form = $this->createForm(ParticipantType::class, ['quiz' => $friendlyId], ['friendlyId' => $friendlyId]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var array{quiz: string, name: string} $formData */
            $formData = $form->getData();

            $quiz = $quizRepository->findOneByFriendlyId($formData['quiz']);
            if ($quiz === null) {
                $form->get('quiz')->addError(new FormError('quiz not found'));

            } else {
                $participant = new Participant();
                $participant->setName($formData['name']);
                $participant->setQuiz($quiz);

                $repository->save($participant);

                return $this->redirectToRoute('app_participant_view', ['id' => $participant->getId()]);
            }
        }

        return $this->render('participant/new.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/{id}/view')]
    public function view(Participant $participant): Response
    {
        return $this->render('participant/view.html.twig', ['participant' => $participant]);
    }
}
