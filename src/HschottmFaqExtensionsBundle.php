<?php

declare(strict_types=1);

/*
 * @copyright  Helmut Schottmüller 2018 <http://github.com/hschottm>
 * @author     Helmut Schottmüller (hschottm)
 * @package    FaqExtensions
 * @license    LGPL-3.0-or-later
 * @see	      https://github.com/hschottm/faqextensions
 */

namespace Hschottm\FaqExtensionsBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Hschottm\FaqExtensionsBundle\DependencyInjection\FaqExtensionsExtension;

class HschottmFaqExtensionsBundle extends Bundle
{
  public function getContainerExtension()
  {
      return new FaqExtensionsExtension();
  }
}
