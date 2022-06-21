<?php

namespace App\Service;

use Knp\Bundle\MarkdownBundle\MarkdownParserInterface;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Cache\CacheInterface;

Class MarkdownHelper
{
    private $markdownParser;
    private $cache;
    private $isDebug;
    private $logger;

    public function __construct(MarkdownParserInterface $markdownParser, CacheInterface $cache, bool $isDebug, LoggerInterface $markdownLogger)
    {
        $this->markdownParser = $markdownParser;
        $this->cache = $cache;    
        $this->debug = $isDebug; // in the services.yaml file we hardcoded this value as a param
        $this->logger = $markdownLogger; // named markdown logger to select it as our logger channel "markdown"
        // something like $mdLogger would use the main logger channel "app"
    }
    public function parse(string $source) : string
    {
        if (stripos($source, 'cat') !== false) {
            $this->logger->info('Meow');
        }

        return $this->cache->get('markdown'.md5($source), function() use ($source) {
            // The above use statement put our needed vairbales within the functions scope
            return $this->markdownParser->transformMarkdown($source);
        });
    }
}
