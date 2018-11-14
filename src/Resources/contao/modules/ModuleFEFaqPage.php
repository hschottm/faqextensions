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
				$objFaq->viewcount++;
				$objFaq->save();
			}
		}
	}
}
