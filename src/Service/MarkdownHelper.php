<?php

namespace App\Service;

use Knp\Bundle\MarkdownBundle\MarkdownParserInterface;
use Symfony\Contracts\Cache\CacheInterface;

Class MarkdownHelper
{
    private $markdownParser;
    private $cache;
    private $isDebug;

    public function __construct(MarkdownParserInterface $markdownParser, CacheInterface $cache, bool $isDebug)
    {
        $this->markdownParser = $markdownParser;
        $this->cache = $cache;    
        $this->debug = $isDebug;
    }
    public function parse(string $source) : string
    {
        return $this->cache->get('markdown'.md5($source), function() use ($source) {
            // The above use statement put our needed vairbales within the functions scope
            return $this->markdownParser->transformMarkdown($source);
        });
    }
}
