<?php

$GLOBALS['TL_DCA']['tl_module']['palettes']['faqtoplist']   = '{title_legend},name,headline,type;{config_legend},faq_limit,faq_categories,faq_readerModule;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID';
$GLOBALS['TL_DCA']['tl_module']['palettes']['faqrecommendedlist']   = '{title_legend},name,headline,type;{config_legend},faq_limit,faq_sortorder,faq_categories,faq_readerModule;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID';
$GLOBALS['TL_DCA']['tl_module']['palettes']['faqhelpfullist']   = '{title_legend},name,headline,type;{config_legend},faq_limit,faq_categories,faq_readerModule;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID';
$GLOBALS['TL_DCA']['tl_module']['palettes']['faqrecentlist']   = '{title_legend},name,headline,type;{config_legend},faq_limit,faq_categories,faq_readerModule;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID';

$GLOBALS['TL_DCA']['tl_module']['fields']['faq_limit'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['faq_limit'],
	'default'                 => '10',
	'search'                  => true,
	'inputType'               => 'text',
	'eval'                    => array('maxlength'=>32, 'style' => 'width: 5em;', 'rgxp' => 'digit', 'tl_class'=>'long', 'decodeEntities' => true),
	'sql'                     => "varchar(32) NOT NULL default '10'"
);

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
