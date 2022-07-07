<?php

namespace App\Service;

use Knp\Bundle\MarkdownBundle\MarkdownParserInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Cache\CacheInterface;

Class MarkdownHelper
{
    private $markdownParser;
    private $cache;
    private $isDebug;
    private $logger;
    private $security;

    public function __construct(MarkdownParserInterface $markdownParser, CacheInterface $cache, bool $isDebug, LoggerInterface $markdownLogger, Security $security)
    {
        $this->markdownParser = $markdownParser;
        $this->cache = $cache;    
        $this->debug = $isDebug; // in the services.yaml file we hardcoded this value as a param
        $this->logger = $markdownLogger; // named markdown logger to select it as our logger channel "markdown"
        // something like $mdLogger would use the main logger channel "app"
        $this->security = $security;
    }
    // pass markdown to be converted to HTML
    public function parse(string $source) : string
    {
        if (stripos($source, 'cat') !== false) {
            $this->logger->info('Meow');
        }
        // check to see if the user is logged in/ exists
        if ($this->security->getUser()) {
            $this->logger->info('Rendering markdown for {user}', [
                'user' => $this->security->getUser()->getUserIdentifier(),
            ]);
        }

        if ($this->isDebug) {
            return $this->markdownParser->transformMarkdown($source);
        }

        return $this->cache->get('markdown'.md5($source), function() use ($source) {
            // The above use statement put our needed vairbales within the functions scope
            return $this->markdownParser->transformMarkdown($source);
        });
    }
}
