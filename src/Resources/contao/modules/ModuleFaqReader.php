<?php

namespace Hschottm\FaqExtensions;

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
