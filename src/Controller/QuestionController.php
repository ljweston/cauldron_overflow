<?php

namespace App\Controller;

use App\Entity\Question;
use App\Repository\AnswerRepository;
use App\Repository\QuestionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
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

    /** only match if page is a digit "\d+"
     * @Route("/{page<\d+>}", name="app_homepage")
     */
    public function homepage(QuestionRepository $repository, int $page = 1)
    {
        $queryBuilder = $repository->createAskedOrderedByNewestQueryBuilder();
        // $html = $twigEnvironment->render('questions/homepage.html.twig'); // returns string with html
        $pagerfanta = new Pagerfanta(
            new QueryAdapter($queryBuilder)
        );
        $pagerfanta->setMaxPerPage(5);
        $pagerfanta->setCurrentPage($page);
        // return new Response($html);
        return $this->render('questions/homepage.html.twig', [
            'pager' => $pagerfanta // passing this object that contains the questions
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
    public function show(Question $question)
    {
        if ($this->isDebug) {
            $this->logger->info('We are in debug mode');
        }
        /**
         * symfony sees the "Question" type hint and looks for the wildcard value of "slug" to query
         * slug matches the property name of our entity "Question"
        */ 

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
