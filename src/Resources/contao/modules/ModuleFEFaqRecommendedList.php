<?php

namespace Hschottm\FaqExtensionsBundle;

use Hschottm\FaqExtensionsBundle\ModuleFEFaqList;

class ModuleFEFaqRecommendedList extends ModuleFEFaqList
{
	protected $strTemplate = 'mod_faqlist_extended';

	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$objTemplate = new \BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### ' . utf8_strtoupper($GLOBALS['TL_LANG']['FMD']['faqrecommendedlist'][0]) . ' ###';
			$objTemplate->title = $this->headline;
			$objTemplate->id = $this->id;
			$objTemplate->link = $this->name;
			$objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

			return $objTemplate->parse();
		}

		return parent::generate();
	}


	protected function compile()
	{
    $limit = null;
		$offset = 0; //(int) $this->skipFirst;

		// Maximum number of items
		if ($this->numberOfItems > 0)
		{
			$limit = $this->numberOfItems;
		}

    $intTotal = \Hschottm\FaqExtensionsBundle\FEFaqModel::countPublishedByPids($this->faq_categories);

    if ($intTotal < 1)
		{
      $this->Template->faq = array();
  		$this->Template->empty = $GLOBALS['TL_LANG']['MSC']['emptyList'];
			return;
		}

    $total = $intTotal - $offset;

    // Split the results
		if ($this->perPage > 0 && (!isset($limit) || $this->numberOfItems > $this->perPage))
		{
			// Adjust the overall limit
			if (isset($limit))
			{
				$total = min($limit, $total);
			}

			// Get the current page
			$id = 'page_n' . $this->id;
			$page = (\Input::get($id) !== null) ? \Input::get($id) : 1;

			// Do not index or cache the page if the page number is outside the range
			if ($page < 1 || $page > max(ceil($total/$this->perPage), 1))
			{
				throw new PageNotFoundException('Page not found: ' . \Environment::get('uri'));
			}

			// Set limit and offset
			$limit = $this->perPage;
			$offset += (max($page, 1) - 1) * $this->perPage;
			$skip = (int) $this->skipFirst;

			// Overall limit
			if ($offset + $limit > $total + $skip)
			{
				$limit = $total + $skip - $offset;
			}

			// Add the pagination menu
			$objPagination = new \Pagination($total, $this->perPage, \Config::get('maxPaginationLinks'), $id);
			$this->Template->pagination = $objPagination->generate("\n  ");
		}

    $params = array('order' => $this->faq_sortorder;
		$objFaq = \Hschottm\FaqExtensionsBundle\FEFaqModel::findPublishedByRecommendationAndPids($this->faq_categories, ($limit ?: 0), $offset, $params);

		// Add the articles
		if ($objFaq !== null)
		{
			$this->Template->faq = $this->parseFaq($objFaq);
		}
  }
}
