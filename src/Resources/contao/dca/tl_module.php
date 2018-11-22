<?php

$GLOBALS['TL_DCA']['tl_module']['palettes']['faqtoplist']   = '{title_legend},name,headline,type;{config_legend},numberOfItems,perPage,faq_categories,faq_readerModule;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID';
$GLOBALS['TL_DCA']['tl_module']['palettes']['faqrecommendedlist']   = '{title_legend},name,headline,type;{config_legend},numberOfItems,perPage,faq_sortorder,faq_categories,faq_readerModule;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID';
$GLOBALS['TL_DCA']['tl_module']['palettes']['faqhelpfullist']   = '{title_legend},name,headline,type;{config_legend},numberOfItems,perPage,faq_categories,faq_readerModule;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID';
$GLOBALS['TL_DCA']['tl_module']['palettes']['faqrecentlist']   = '{title_legend},name,headline,type;{config_legend},numberOfItems,perPage,faq_categories,faq_readerModule;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID';

$GLOBALS['TL_DCA']['tl_module']['fields']['faq_sortorder'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['faq_sortorder'],
	'exclude'                 => true,
	'inputType'               => 'select',
	'options'                 => array('sort_alpha_asc', 'sort_alpha_desc', 'sort_created_desc', 'sort_created_asc'),
	'reference'               => &$GLOBALS['TL_LANG']['tl_module'],
	'sql'                     => "varchar(20) NOT NULL default 'sort_alpha_asc'"
);

class tl_module_faq_extensions extends Backend
{

	public function getSortValues()
	{
		$arrSortOrder = array(
			0 => $GLOBALS['TL_LANG']['tl_module']['sort_order_alpha'],
			1 => $GLOBALS['TL_LANG']['tl_module']['sort_order_created']
		);
		return $arrSortOrder;
	}
}
