<?php

namespace App\DataFixtures;

use App\Entity\Answer;
use App\Entity\Question;
use App\Factory\QuestionFactory;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        QuestionFactory::new()->createMany(20);
        // generate unpublished questions
        QuestionFactory::new()
            ->unpublished()
            ->createMany(5)
        ;

        $answer = new Answer();
        $answer->setContent('This is a question test. I do not know the answer, lol.');
        $answer->setUsername('weaverryan');

        $question = new Question();
        $question->setName('How to magically not be broke.');
        $question->setQuestion('... I am broke. How do I make money?');
        // set the relationship btwn ans and ques
        $answer->setQuestion($question);

        $manager->persist($answer);
        $manager->persist($question);

        $manager->flush();
    }
}
