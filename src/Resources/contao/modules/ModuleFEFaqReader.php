<?php

namespace Hschottm\FaqExtensionsBundle;

class ModuleFEFaqReader extends \Contao\ModuleFaqReader
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
    $objFaq = \Contao\FaqModel::findPublishedByParentAndIdOrAlias(\Contao\Input::get('auto_item'), $this->faq_categories);
		if (null !== $objFaq)
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

		parent::compile();

    if (null !== $objFaq)
		{
      $this->Template->showhelpful = $objFaq->showhelpful;
      $this->Template->helpful = $objFaq->helpful;
      $this->Template->nothelpful = $objFaq->nothelpful;
      $this->Template->alias = $objFaq->alias;
    }
	}
}
