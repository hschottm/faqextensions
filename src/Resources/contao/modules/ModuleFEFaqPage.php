<?php

namespace Hschottm\FaqExtensionsBundle;
use Psr\Log\LogLevel;
use Contao\CoreBundle\Monolog\ContaoContext;
use Contao\ModuleFaqPage;
use Contao\FaqModel;

class ModuleFEFaqPage extends \Contao\ModuleFaqPage
{
  private function http_strip_query_param($url, $param)
  {
    $pieces = parse_url($url);
    if (empty($pieces['query'])) {
        return $url;
    }

    $query = [];
    parse_str($pieces['query'], $query);
    if (!isset($query[$param])) {
        return $url;
    }

    unset($query[$param]);
    $pieces['query'] = http_build_query($query);
    $result = $pieces['path'];
    if ($pieces['query'])
    {
      $result .= "?";
    }
    $result .= $pieces['query'];
    return $result;
  }

	protected function compile()
	{
    $objFaq = \Contao\FaqModel::findPublishedByPids($this->faq_categories);
		if ($objFaq !== null)
		{
			while ($objFaq->next())
			{
        if ($objFaq->showhelpful && in_array(\Contao\Input::get('vote'), ['1', '-1'], true))
        {
          $alias = \Contao\Input::get('tl_faq');
          if ((string) $alias === (string) ($objFaq->alias ?: $objFaq->id))
          {

            if (intval(\Contao\Input::get('vote')) == 1)
            {
              $objFaq->helpful++;
            }
            else if (intval(\Contao\Input::get('vote')) == -1)
            {
              $objFaq->nothelpful++;
            }
            $requestUri = \Contao\Environment::get('requestUri');
            $requestUri = $this->http_strip_query_param($requestUri, "vote");
            $requestUri = $this->http_strip_query_param($requestUri, "tl_faq");
            $objFaq->save();
            \Contao\Controller::redirect($requestUri);
          }
        }
        $objFaq->viewcount++;
				$objFaq->save();
			}
		}

		parent::compile();

        // Expose each legacy stdClass item as a Twig include context.
        $categories = $this->Template->faq ?? [];
        foreach ($categories as &$category) {
            $category['items'] = array_map(static fn ($item) => (array) $item, $category['items']);
        }
        unset($category);
        $this->Template->faq = $categories;
	}
}
