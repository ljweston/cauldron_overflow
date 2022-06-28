<?php

namespace App\DataFixtures;

use App\Entity\Answer;
use App\Entity\Question;
use App\Factory\AnswerFactory;
use App\Factory\QuestionFactory;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $questions = QuestionFactory::new()->createMany(20);
        // generate unpublished questions
        // QuestionFactory::new()
        //     ->unpublished()
        //     ->createMany(5)
        // ;

        QuestionFactory::new()
            ->unpublished()
            ->many(5)
            ->create()
        ;

        AnswerFactory::createMany(100, function() use ($questions) {
            return [
                'question' => $questions[array_rand($questions)]
            ];
        });
        AnswerFactory::new(function() use ($questions) {
            return [
                'question' => $questions[array_rand($questions)]
            ];
        })->needsApproval()->many(20)->create();

        $manager->flush();
        // AnswerFactory::createMany(100, function() use ($questions){
        //     return [
        //         'question' => $questions[array_rand($questions)],
        //     ];
        // });

        // AnswerFactory::new(function() use ($questions) {
        //     return [
        //         'question' => $questions[array_rand($questions)],
        //     ];
        // })->needsApproval()->many(20)->create();
        // $manager->flush();
    }
}
