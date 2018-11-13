<?php

namespace Hschottm\FaqExtensionsBundle;

class ModuleFEFaqReader extends \Contao\ModuleFaqReader
{
  /*
	protected function compile()
	{
		\Contao\ModuleFaqReader::compile();
		$objFaq = \Hschottm\FaqExtensionsBundle::findPublishedByParentAndIdOrAlias(\Input::get('items'), $this->faq_categories);
		if (null !== $objFaq)
		{
			$objFaq->viewcount++;
			$objFaq->save();
		}
	}
  */
}
