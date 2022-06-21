<?php

namespace App\Controller;

use App\Service\MarkdownHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class QuestionController extends AbstractController
{
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
     * @Route("/questions/{slug}", name="app_question_show")
     */
    public function show($slug, MarkdownHelper $markdownHelper)
    {
        dump($this->getParameter('cache_adapter'));
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
