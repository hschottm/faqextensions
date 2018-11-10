<?php

/**
 * @copyright  Helmut Schottmüller 2018 <http://github.com/hschottm>
 * @author     Helmut Schottmüller (hschottm)
 * @package    FaqExtensions
 * @license    LGPL-3.0+
 * @see	       https://github.com/hschottm/faqextensions
 *
 */

namespace Hschottm\FaqExtensionsBundle\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Hschottm\FaqExtensionsBundle\FaqExtensionsBundle;

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
            BundleConfig::create(FaqExtensionsBundle::class)
                ->setLoadAfter([ContaoCoreBundle::class]),
        ];
    }
}