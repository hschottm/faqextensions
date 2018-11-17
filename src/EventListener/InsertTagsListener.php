<?php

namespace Hschottm\FaqExtensionsBundle\EventListener;

use Contao\Config;
use Contao\CoreBundle\Framework\ContaoFrameworkInterface;
use Contao\FaqCategoryModel;
use Contao\FaqModel;
use Contao\PageModel;
use Contao\StringUtil;

class InsertTagsListener
{
    /**
     * @var ContaoFrameworkInterface
     */
    private $framework;

    /**
     * @var array
     */
    private $supportedTags = [
      'faq_helpful_url',
      'faq_nothelpful_url'
    ];

    /**
     * Constructor.
     *
     * @param ContaoFrameworkInterface $framework
     */
    public function __construct(ContaoFrameworkInterface $framework)
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
      return \Environment::get('uri');
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
        switch ($key) {
          case 'faq_helpful_url':
            if (\Environment::get('queryString'))
            {
              return $url . "&amp;vote=1&amp;tl_faq=" . $faq->alias;
            }
            else {
              return $url . "?vote=1&amp;tl_faq=" . $faq->alias;
            }
          case 'faq_nothelpful_url':
          if (\Environment::get('queryString'))
          {
            return $url . "&amp;vote=-1&amp;tl_faq=" . $faq->alias;
          }
          else {
            return $url . "?vote=-1&amp;tl_faq=" . $faq->alias;
          }
        }

        return false;
    }
}
