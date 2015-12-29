<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2015 Leo Feyer
 *
 * @license LGPL-3.0+
 */

namespace FaqExtensions;


/**
 * Class ModuleFaqPageExtended
 *
 * @author Leo Feyer <https://github.com/hschottm>
 */
class ModuleFaqPage extends \Contao\ModuleFaqPage
{
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
}
