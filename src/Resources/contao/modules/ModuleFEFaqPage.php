<?php

namespace Hschottm\FaqExtensionsBundle;
use Psr\Log\LogLevel;
use Contao\CoreBundle\Monolog\ContaoContext;
use Contao\ModuleFaqPage;
use Contao\FaqModel;

class ModuleFEFaqPage extends \Contao\ModuleFaqPage
{
	protected function compile()
	{
		\Contao\ModuleFaqPage::compile();

    $objFaq = \Contao\FaqModel::findPublishedByPids($this->faq_categories);

		if ($objFaq !== null)
		{
			while ($objFaq->next())
			{
        $votecalled = false;
        if (\Environment::get('vote'))
        {
          $votecalled = true;
          if (intval(\Environment::get('vote')) == 1)
          {
            $objFaq->helpful++;
          }
          else if (intval(\Environment::get('vote')) == -1)
          {
            $objFaq->nothelpful++;
          }
        }
        if (!$votecalled) $objFaq->viewcount++;
				$objFaq->save();
			}
		}
	}
}
