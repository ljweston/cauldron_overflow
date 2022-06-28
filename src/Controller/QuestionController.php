<?php

namespace App\Controller;

use App\Entity\Question;
use App\Entity\Answer;
use App\Repository\AnswerRepository;
use App\Repository\QuestionRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class QuestionController extends AbstractController
{
    private $logger;
    private $isDebug;
    public function __construct(LoggerInterface $logger, bool $isDebug)
    {
        $this->logger = $logger;
        $this->isDebug = $isDebug;
    }
    // #[Route('/', name:"app_homepage")] // php 8 feature
    /**
     * @Route("/", name="app_homepage")
     */
    public function homepage(QuestionRepository $repository)
    {
        $questions = $repository->findAllAskedOrderByNewest();
        // $html = $twigEnvironment->render('questions/homepage.html.twig'); // returns string with html

        // return new Response($html);
        return $this->render('questions/homepage.html.twig', [
            'questions' => $questions
        ]);

        /**
         * What we see below is how we normally get a repository. But we do not need to autowire the EntityManager
         * The question repo is a service in the container.
         * // $repository = $entityManager->getRepository(Question::class); // fetching repo
         * // $questions = $repository->findBy([], ['askedAt' => 'DESC']); // specify in desc order
         */
    }

    /**
     * @Route("/questions/new")
     */
    public function new(EntityManagerInterface $entityManager )
    {
        return new Response('This sounds like a great feature for V2');
    }

    /**
     * @Route("/questions/{slug}", name="app_question_show")
     */
    public function show(Question $question, EntityManagerInterface $entityManager)
    {
        if ($this->isDebug) {
            $this->logger->info('We are in debug mode');
        }
        /**
         * symfony sees the "Question" type hint and looks for the wildcard value of "slug" to query
         * slug matches the property name of our entity "Question"
        */ 
        // $repository = $entityManager->getRepository(Answer::class);
        // dd($repository->findBy(['question'=> $question]));
        // question->getAnswers() does NOT get an array of answers is some sort of Doctrine Collection object
        // $answers = $question->getAnswers(); // There is an easier way
        // lazy loading: only do the query and loading when we ask it to. (like in the loop)

        // recall that the controllers always require a RESPONSE OBJ be returned.
        // THUS: render returns a response object
        return $this->render('questions/show.html.twig', [
            // array of data or vars passed in
            'question' => $question, // question has getAnswers(), use in twig
        ]);

        // return new Response(sprintf(
        //     'Future page to show a question "%s"!',
        //     ucwords(str_replace('-', '', $slug)) 
        // ));
    }

    /**
     * @Route("/questions/{slug}/vote", name="app_question_vote", methods="POST")
     */
    public function questionVote(Question $question, Request $request, EntityManagerInterface $entityManager)
    {
        // Request is not a type hint. It is data from our form we submit
        $direction = $request->request->get('direction');

        if ($direction === 'up') {
            $question->upVote();
        } elseif ($direction === 'down') {
            $question->downVote();
        }

        // SAVE
        $entityManager->flush();

        return $this->redirectToRoute('app_question_show', [
            'slug' => $question->getSlug(),
        ]);
    }

}
