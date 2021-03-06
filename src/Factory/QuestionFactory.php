<?php

namespace App\Factory;
use App\Entity\Question;
use App\Repository\QuestionRepository;
use DateTime;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
/**
 * @method static Question|Proxy findOrCreate(array $attributes)
 * @method static Question|Proxy random()
 * @method static Question[]|Proxy[] randomSet(int $number)
 * @method static Question[]|Proxy[] randomRange(int $min, int $max)
 * @method static QuestionRepository|RepositoryProxy repository()
 * @method Question|Proxy create($attributes = [])
 * @method Question[]|Proxy[] createMany(int $number, $attributes = [])
 */
final class QuestionFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();

        // TODO inject services if required (https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services)
    }
    
    public function unpublished(): self
    {
        return $this->addState(['askedAt' => null]);
    }

    protected function getDefaults(): array
    {
        return [
            'name' => self::faker()->realText(50),
            // removed slug to instantiate below in init
            'question' => self::faker()->paragraphs(
                self::faker()->numberBetween(1, 4),
                true
            ),
            'askedAt' => self::faker()->dateTimeBetween('-100 days', '-1 minute'),
            'votes' => rand(-20, 50),
            'owner' => UserFactory::new(),
        ];
    }
    // Add hooks!
    protected function initialize(): self
    {
        // see https://github.com/zenstruck/foundry#initialization
        return $this;
            // ->afterInstantiate(function(Question $question) {
    }
    
    protected static function getClass(): string
    {
        return Question::class;
    }
}
