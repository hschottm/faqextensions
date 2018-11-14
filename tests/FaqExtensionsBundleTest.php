<?php

/*
 * @copyright  Helmut Schottmüller 2018 <http://github.com/hschottm>
 * @author     Helmut Schottmüller (hschottm)
 * @package    FaqExtensions
 * @license    LGPL-3.0-or-later
 * @see	      https://github.com/hschottm/faqextensions
 */

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
