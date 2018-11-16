<?php

/*
 * @copyright  Helmut Schottmüller 2018 <http://github.com/hschottm>
 * @author     Helmut Schottmüller (hschottm)
 * @package    FaqExtensions
 * @license    LGPL-3.0-or-later
 * @see	      https://github.com/hschottm/faqextensions
 */

namespace Hschottm\FaqExtensionsBundle\Tests;

use Contao\CoreBundle\Framework\FrameworkAwareInterface;
use Hschottm\FaqExtensionsBundle\HschottmFaqExtensionsBundle;
use Hschottm\FaqExtensionsBundle\DependencyInjection\FaqExtensionsExtension;
use Hschottm\FaqExtensionsBundle\EventListener\InsertTagsListener;

use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

use PHPUnit\Framework\TestCase;

class FaqExtensionsBundleTest extends TestCase
{
  /**
   * @var ContainerBuilder
   */
  private $container;

  /**
   * {@inheritdoc}
   */
  protected function setUp()
  {
      parent::setUp();

      $this->container = new ContainerBuilder(new ParameterBag(['kernel.debug' => false]));

      $extension = new HschottmFaqExtensionsBundle();
      $extension->load([], $this->container);
  }

  /**
   * Tests the contao_faq.listener.insert_tags service.
   */
  public function testRegistersTheInsertTagsListener()
  {
      $this->assertTrue($this->container->has('contao_faqextensions.listener.insert_tags'));

      $definition = $this->container->getDefinition('contao_faqextensions.listener.insert_tags');

      $this->assertSame(InsertTagsListener::class, $definition->getClass());
      $this->assertTrue($definition->isPublic());
      $this->assertSame('contao.framework', (string) $definition->getArgument(0));
  }
}
