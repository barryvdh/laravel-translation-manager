<?php

namespace Barryvdh\TranslationManager\Tests;

use Barryvdh\TranslationManager\Manager;

class ManagerTest extends TestCase
{
    public function testResolveManager(): void
    {
        /** @var Manager $manager */
        $manager = app(Manager::class);

        $this->assertIsArray($manager->getConfig());
    }

}
