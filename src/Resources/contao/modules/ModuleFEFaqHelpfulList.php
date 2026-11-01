<?php

namespace Hschottm\FaqExtensionsBundle;

use Hschottm\FaqExtensionsBundle\ModuleFEFaqList;

class ModuleFEFaqHelpfulList extends ModuleFEFaqList
{
	protected $strTemplate = 'mod_faqhelpfullist';

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
			$page = \Contao\Input::get($id) ?? '1';
			if (!ctype_digit((string) $page)) {
				throw new \Contao\CoreBundle\Exception\PageNotFoundException('Invalid page number');
			}
			$page = (int) $page;

			// Do not index or cache the page if the page number is outside the range
			if ($page < 1 || $page > max(ceil($total/$this->perPage), 1))
			{
				throw new \Contao\CoreBundle\Exception\PageNotFoundException('Page not found: ' . \Contao\Environment::get('uri'));
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
			$objPagination = new \Contao\Pagination($total, $this->perPage, \Contao\Config::get('maxPaginationLinks'), $id);
			$this->Template->pagination = $objPagination->generate("\n  ");
		}

		$objFaq = \Hschottm\FaqExtensionsBundle\FEFaqModel::findPublishedByHelpfulAndPids($this->faq_categories, ($limit ?: 0), $offset);

		// Add the articles
		if ($objFaq !== null)
		{
			$this->Template->faq = $this->parseFaq($objFaq);
		}
	}
}
