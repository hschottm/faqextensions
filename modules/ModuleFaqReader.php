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
 * Class ModuleFaqReader
 *
 * @author Leo Feyer <https://github.com/hschottm>
 */
class ModuleFaqReader extends \Contao\ModuleFaqReader
{
	protected function compile()
	{
		\Contao\ModuleFaqReader::compile();
		$objFaq = \FaqModel::findPublishedByParentAndIdOrAlias(\Input::get('items'), $this->faq_categories);
		if (null !== $objFaq)
		{
			$objFaq->viewcount++;
			$objFaq->save();
		}
	}
}
