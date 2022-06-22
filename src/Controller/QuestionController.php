<?php

namespace App\Controller;

use App\Entity\Question;
use App\Service\MarkdownHelper;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

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
    public function homepage(Environment $twigEnvironment)
    {
        // $html = $twigEnvironment->render('questions/homepage.html.twig'); // returns string with html

        // return new Response($html);
        return $this->render('questions/homepage.html.twig');
    }

    /**
     * @Route("/questions/new")
     */
    public function new(EntityManagerInterface $entityManager )
    {
        $question = new Question();

        $question->setName("Magic Missing Pants")
            ->setSlug("magic-missing-pants-".rand(0, 1000))
            ->setQuestion(<<<EOF
            Hi! So... I'm having a *weird* day. Yesterday, I cast a spell
            to make my dishes wash themselves. But while I was casting it,
            I slipped a little and I think `I also hit my pants with the spell`.
            When I woke up this morning, I caught a quick glimpse of my pants
            opening the front door and walking out! I've been out all afternoon
            (with no pants mind you) searching for them.
            Does anyone have a spell to call your pants back?
            EOF);
            
            if (rand(1, 10) > 2) {
                $question->setAskedAt(new DateTimeImmutable(sprintf('-%d days', rand(1, 100))));
            }

            $entityManager->persist($question);
            $entityManager->flush();

        return new Response(sprintf(
            "Well hallo! The shiny new question is #%d, slug %s", 
            $question->getId(),
            $question->getSlug(),
        ));
    }

    /**
     * @Route("/questions/{slug}", name="app_question_show")
     */
    public function show($slug, MarkdownHelper $markdownHelper, EntityManagerInterface $entityManager)
    {
        if ($this->isDebug) {
            $this->logger->info('We are in debug mode');
        }

        $repository = $entityManager->getRepository(Question::class);
        /** @var Question|null $question */
        $question = $repository->findOneby(['slug' => $slug]);
        if (!$question) {
            throw $this->createNotFoundException(sprintf('No question found for slug "%s"', $slug));
        }

        $answers = [
            'Make sure the cat is sitting `purrrfectly` still',
            'Fuzzy slippers',
            'This isn\'t a question so much as it is a statement I think'
        ];
        
        $questionText = 'I\'ve been turned into a cat, any thoughts on how to turn back? While I\'m **adorable**, I don\'t really care for cat food.';
        
        $parsedQuestionText = $markdownHelper->parse($questionText);

        // dump($this);

        // recall that the controllers always require a RESPONSE OBJ be returned.
        // THUS: render returns a response object
        return $this->render('questions/show.html.twig', [
            // array of data or vars passed in
            'question' => ucwords(str_replace('-', ' ', $slug)),
            'questionText' => $parsedQuestionText,
            'answers' => $answers
        ]);

        // return new Response(sprintf(
        //     'Future page to show a question "%s"!',
        //     ucwords(str_replace('-', '', $slug)) 
        // ));
    }

}
