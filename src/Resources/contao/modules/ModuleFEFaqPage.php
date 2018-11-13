<?php

namespace Hschottm\FaqExtensionsBundle;
use Psr\Log\LogLevel;
use Contao\CoreBundle\Monolog\ContaoContext;

class ModuleFEFaqPage extends \Contao\ModuleFaqPage
{
  /*
	protected function compile()
	{
		\Contao\ModuleFaqPage::compile();

    $objFaq = \FaqModel::findPublishedByPids($this->faq_categories);

		if ($objFaq !== null)
		{
			while ($objFaq->next())
			{
				$objFaq->viewcount++;
				$objFaq->save();
			}
		}
	}
  */
}
