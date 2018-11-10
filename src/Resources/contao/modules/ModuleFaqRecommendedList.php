<?php

namespace Hschottm\FaqExtensionsBundle;

class ModuleFaqRecommendedList extends \Contao\ModuleFaqList
{
	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_faqlist_top';

	/**
	 * Display a wildcard in the back end
	 *
	 * @return string
	 */
	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			/** @var \BackendTemplate|object $objTemplate */
			$objTemplate = new \BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### ' . utf8_strtoupper($GLOBALS['TL_LANG']['FMD']['faqrecommendedlist'][0]) . ' ###';
			$objTemplate->title = $this->headline;
			$objTemplate->id = $this->id;
			$objTemplate->link = $this->name;
			$objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

			return $objTemplate->parse();
		}

		// Show the FAQ reader if an item has been selected
		if ($this->faq_readerModule > 0 && (isset($_GET['items']) || (\Config::get('useAutoItem') && isset($_GET['auto_item']))))
		{
			return $this->getFrontendModule($this->faq_readerModule, $this->strColumn);
		}

		return parent::generate();
	}


	/**
	 * Generate the module
	 */
	protected function compile()
	{
		$objFaq = \Hschottm\FaqExtensionsBundle\FaqModel::findPublishedByRecommendation(array("order" => $this->faq_sortorder, "limit" => (($this->faq_limit > 0) ? $this->faq_limit : 10)));

		if ($objFaq === null)
		{
			$this->Template->faq = array();

			return;
		}

		$arrFaq = array();

		// Add FAQs
		while ($objFaq->next())
		{
			$arrTemp = $objFaq->row();
			$arrTemp['title'] = specialchars($objFaq->question, true);
			$arrTemp['href'] = $this->generateFaqLink($objFaq);

			/** @var \FaqCategoryModel $objPid */
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

		$this->Template->faq = $arrFaq;
	}
}
