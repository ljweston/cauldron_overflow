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
        QuestionFactory::new()->createMany(20);
        // generate unpublished questions
        QuestionFactory::new()
            ->unpublished()
            ->createMany(5)
        ;

        AnswerFactory::createMany(100);

        $manager->flush();
    }
}
