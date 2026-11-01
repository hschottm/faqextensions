<?php

/*
 * @copyright  Helmut Schottmüller 2018 <http://github.com/hschottm>
 * @author     Helmut Schottmüller (hschottm)
 * @package    FaqExtensions
 * @license    LGPL-3.0-or-later
 * @see	      https://github.com/hschottm/faqextensions
 */

namespace Hschottm\FaqExtensionsBundle\EventListener;

use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\FaqModel;

class InsertTagsListener
{
    /**
     * @var ContaoFramework
     */
    private $framework;

    /**
     * @var array
     */
    private $supportedTags = [
      'faq_helpful_url',
      'faq_nothelpful_url',
    ];

    /**
     * Constructor.
     *
     * @param ContaoFramework $framework
     */
    public function __construct(ContaoFramework $framework)
    {
        $this->framework = $framework;
    }

    /**
     * Replaces FAQ insert tags.
     *
     * @param string $tag
     *
     * @return string|false
     */
    public function onReplaceInsertTags($tag)
    {
        $elements = explode('::', $tag);
        $key = strtolower($elements[0]);
        if (!\in_array($key, $this->supportedTags, true)) {
            return false;
        }

        if (empty($elements[1])) {
            return '';
        }

        $this->framework->initialize();

        /** @var FaqModel $adapter */
        $adapter = $this->framework->getAdapter(FaqModel::class);

        $faq = $adapter->findByIdOrAlias($elements[1]);

        if (null === $faq || false === ($url = $this->generateUrl($faq))) {
            return '';
        }

        return $this->generateReplacement($faq, $key, $url);
    }

    /**
     * Generates the URL for an FAQ.
     *
     * @param FaqModel $faq
     *
     * @return string|false
     */
    private function generateUrl(FaqModel $faq)
    {
        return \Contao\Environment::get('uri');
    }

    /**
     * Generates the replacement string.
     *
     * @param FaqModel $faq
     * @param string   $key
     * @param string   $url
     *
     * @return string|false
     */
    private function generateReplacement(FaqModel $faq, $key, $url)
    {
        $parts = parse_url($url);
        $query = [];
        parse_str($parts['query'] ?? '', $query);
        $query['vote'] = $key === 'faq_helpful_url' ? 1 : -1;
        $query['tl_faq'] = $faq->alias ?: $faq->id;

        return \Contao\StringUtil::specialcharsUrl(
            ($parts['path'] ?? '/') . '?' . http_build_query($query)
        );
    }
}
