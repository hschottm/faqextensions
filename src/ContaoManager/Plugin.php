<?php

declare(strict_types=1);

/**
 * @copyright  Helmut Schottmüller 2018 <http://github.com/hschottm>
 * @author     Helmut Schottmüller (hschottm)
 * @package    FaqExtensions
 * @license    LGPL-3.0+
 * @see	       https://github.com/hschottm/faqextensions
 *
 */

namespace Hschottm\FaqExtensionsBundle\ContaoManager;

use Hschottm\FaqExtensionsBundle\HschottmFaqExtensionsBundle;
use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;

/**
 * Plugin for the Contao Manager.
 *
 * @author Helmut Schottmüller (hschottm)
 */
class Plugin implements BundlePluginInterface
{
    /**
     * {@inheritdoc}
     */
     public function getBundles(ParserInterface $parser)
     {
         return [
             BundleConfig::create(HschottmFaqExtensionsBundle::class)
                 ->setLoadAfter([ContaoCoreBundle::class])
                 ->setReplace(['faqextensions']),
         ];
      }
}
