<?php

namespace App\DataFixtures;

use App\Entity\Tag;
use App\Factory\AnswerFactory;
use App\Factory\QuestionFactory;
use App\Factory\TagFactory;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        TagFactory::createMany(100);

        $questions = QuestionFactory::new()->createMany(20, function() {
            return [
            'tags' => TagFactory::randomRange(0,5),
            ];
        });
        // generate unpublished questions
        QuestionFactory::new()
            ->unpublished()
            ->createMany(5)
        ;

        AnswerFactory::createMany(100, function() use ($questions){
            return [
                'question' => $questions[array_rand($questions)],
            ];
        });

        AnswerFactory::new(function() use ($questions){
            return [
                'question' => $questions[array_rand($questions)],
            ];
        })->needsApproval()->many(20)->create();

        $question = QuestionFactory::createOne();

        $tag1 = new Tag();
        $tag1->setName('Dinosaurs');
        $tag2 = new Tag();
        $tag2->setName('Monster Trucks');

        // realte question to a tag!
        $question->addTag($tag1);
        $question->addTag($tag2);

        $manager->persist($tag1);
        $manager->persist($tag2);

        $manager->flush();
    }
}
