<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class QuestionController extends AbstractController
{
    // #[Route('/', name:"app_homepage")] // php 8 feature
    /**
     * @Route("/", name="app_homepage")
     */
    public function homepage()
    {
        return $this->render('questions/homepage.html.twig');
    }

    /**
     * @Route("/questions/{slug}", name="app_question_show")
     */
    public function show($slug)
    {
        $answers = [
            'Make sure the cat is sitting still',
            'Fuzzy slippers',
            'This isn\'t a question so much as it is a statement I think'
        ];

        dump($this);
        // recall that the controllers always require a RESPONSE OBJ be returned.
        // THUS: render returns a response object
        return $this->render('questions/show.html.twig', [
            // array of data or vars passed in
            'question' => ucwords(str_replace('-', ' ', $slug)),
            'answers' => $answers
        ]);

        // return new Response(sprintf(
        //     'Future page to show a question "%s"!',
        //     ucwords(str_replace('-', '', $slug)) 
        // ));
    }
}
