<?php

namespace Tests\Feature;

use Codewiser\Polyglot\GettextPopulator;
use Codewiser\Polyglot\StringsCollector;
use Sepia\PoParser\Parser;
use Sepia\PoParser\SourceHandler\FileSystem;
use Tests\TestCase;

class ContextTest extends TestCase
{
    public function testReading()
    {
        /** @var StringsCollector $collector */
        $collector = app(StringsCollector::class);
        $collector->collect();

        $file = new FileSystem($collector->getPortableObjectTemplate());
        $parser = new Parser($file);
        $catalog = $parser->parse();

        $this->assertNotNull($catalog->getEntry('One apple'));
        $this->assertNotNull($catalog->getEntry('One apple', 'Apple context'));
    }
}