<?php

namespace Barryvdh\TranslationManager\Tests;

use Barryvdh\TranslationManager\Manager;

class PdfTest extends TestCase
{
    public function testResolveManager(): void
    {
        /** @var Manager $manager */
        $manager = app(Manager::class);

        $this->assertIsArray($manager->getConfig());
    }

}
