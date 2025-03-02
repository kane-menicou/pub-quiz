<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\Quiz;
use App\Form\ParticipantType;
use App\Form\QuizType;
use App\Repository\ParticipantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/participants')]
class ParticipantController extends AbstractController
{
    #[Route('/new')]
    public function new(Request $request, ParticipantRepository $repository): Response
    {
        $form = $this->createForm(ParticipantType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Participant $participant */
            $participant = $form->getData();

            $repository->save($participant);

            return $this->redirectToRoute('app_participant_view', ['id' => $participant->getId()]);
        }

        return $this->render('participant/new.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/{id}/view')]
    public function view(Participant $participant): Response
    {
        return $this->render('participant/view.html.twig', ['participant' => $participant]);
    }
}
