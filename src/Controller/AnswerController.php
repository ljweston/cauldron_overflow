<?php

namespace App\Controller;

use App\Entity\Answer;
use App\Repository\AnswerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class AnswerController extends BaseController
{
    /**
     * @Route("/answers/{id}/vote", name="answer_vote", methods="POST")
     * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
     */
    public function answerVote(Answer $answer, LoggerInterface $logger, Request $request, EntityManagerInterface $entityManager)
    {
        $logger->info('{user} is voting on answer {answer}', [
            'user' => $this->getUser()->getEmail(),
            'answer' => $answer->getId(),
        ]);

        $data = json_decode($request->getContent(), true);
        // $data = json_decode($data['data'], true);
        $direction = $data['data']['direction'] ?? 'up';
        // use real logic here to save this to the database
        if ($direction === 'up') {
            $logger->info('Voting up!');
            $answer->setVotes($answer->getVotes() + 1);
            // $currentVoteCount = rand(7, 100);
        } else {
            $logger->info('Voting down!');
            $answer->setVotes($answer->getVotes() - 1);
        }
        // save the new count
        $entityManager->flush();

        return $this->json(['votes' => $answer->getVotes()]);
    }
    /**
     * @Route("/answers/popular", name="app_popular_answers")
     */
    public function popularAnswers(AnswerRepository $answerRepository, Request $request)
    {
        $answers = $answerRepository->findMostPopular(
            $request->query->get('q')
        );

        return $this->render('answers/popularAnswers.html.twig', [
            'answers' => $answers,
        ]);
    }
}
