<?php

namespace Hschottm\FaqExtensionsBundle\Tests;

use Hschottm\FaqExtensionsBundle\HschottmFaqExtensionsBundle;
use PHPUnit\Framework\TestCase;

class FaqExtensionsBundleTest extends TestCase
{
    public function testCanBeInstantiated()
    {
        $bundle = new HschottmFaqExtensionsBundle();

        $this->assertInstanceOf('Hschottm\FaqExtensionsBundle\HschottmFaqExtensionsBundle', $bundle);
    }
}
