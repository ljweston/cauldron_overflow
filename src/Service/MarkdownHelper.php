<?php

namespace App\Service;

use Knp\Bundle\MarkdownBundle\MarkdownParserInterface;
use Symfony\Contracts\Cache\CacheInterface;

Class MarkdownHelper
{
    private $markdownParser;
    private $cache;

    public function __construct(MarkdownParserInterface $markdownParser, CacheInterface $cache)
    {
        $this->markdownParser = $markdownParser;
        $this->cache = $cache;    
    }
    public function parse(string $source) : string
    {
        return $this->cache->get('markdown'.md5($source), function() use ($source) {
            // The above use statement put our needed vairbales within the functions scope
            return $this->markdownParser->transformMarkdown($source);
        });
    }
}
