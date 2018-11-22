<?php

namespace Hschottm\FaqExtensionsBundle;

class ModuleFEFaqList extends \Contao\ModuleFaqList
{
  /**
	 * Parse one or more items and return them as array
	 *
	 * @param Model\Collection $objArticles
	 * @param boolean          $blnAddArchive
	 *
	 * @return array
	 */
	protected function parseFaq($objFaq)
	{
		$limit = $objFaq->count();

		if ($limit < 1)
		{
			return array();
		}

		$count = 0;
		$arrFaq = array();

		// Add FAQs
		while ($objFaq->next())
		{
			$arrTemp = $objFaq->row();
			$arrTemp['title'] = specialchars($objFaq->question, true);
			$arrTemp['href'] = $this->generateFaqLink($objFaq);

			$objPid = $objFaq->getRelated('pid');

			array_push($arrFaq, $arrTemp);
		}

		$faq_count = 0;
		$faq_limit = count($arrFaq);

		// Add classes
		foreach ($arrFaq as $k=>$v)
		{
			$arrFaq[$k]['class'] = trim(((++$faq_count == 1) ? ' first' : '') . (($faq_count >= $faq_limit) ? ' last' : '') . ((($faq_count % 2) == 0) ? ' odd' : ' even'));
		}
		return $arrFaq;
	}
}
