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
    if (!$pieces['query']) {
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
        if (\Input::get('vote'))
        {
          $alias = \Input::get('tl_faq');
          if (strcmp($alias, $objFaq->alias) == 0)
          {
            $votecalled = true;
            if (intval(\Input::get('vote')) == 1)
            {
              $objFaq->helpful++;
            }
            else if (intval(\Input::get('vote')) == -1)
            {
              $objFaq->nothelpful++;
            }
            $requestUri = \Environment::get('requestUri');
            $requestUri = $this->http_strip_query_param($requestUri, "vote");
            $requestUri = $this->http_strip_query_param($requestUri, "tl_faq");
            $objFaq->save();
            \Controller::redirect($requestUri);
          }
        }
        $objFaq->viewcount++;
				$objFaq->save();
			}
		}

		\Contao\ModuleFaqPage::compile();
	}
}
