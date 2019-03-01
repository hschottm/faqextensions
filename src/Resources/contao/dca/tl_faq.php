<?php

System::loadLanguageFile('tl_content');

$GLOBALS['TL_DCA']['tl_faq']['palettes']['default'] = str_replace('noComments', 'noComments,recommended,showhelpful', $GLOBALS['TL_DCA']['tl_faq']['palettes']['default']);

$GLOBALS['TL_DCA']['tl_faq']['fields']['viewcount'] = array(
	'sql'                     => "int(10) unsigned NOT NULL default '0'"
);

$GLOBALS['TL_DCA']['tl_faq']['fields']['helpful'] = array(
	'sql'                     => "int(10) NOT NULL default '0'"
);

$GLOBALS['TL_DCA']['tl_faq']['fields']['nothelpful'] = array(
	'sql'                     => "int(10) NOT NULL default '0'"
);

$GLOBALS['TL_DCA']['tl_faq']['fields']['noComments']['eval'] = array('tl_class'=>'w50 m12');

$GLOBALS['TL_DCA']['tl_faq']['fields']['recommended'] = array(
	'label'                   => &$GLOBALS['TL_LANG']['tl_faq']['recommended'],
	'exclude'                 => true,
	'filter'                  => true,
	'eval'                    => array('tl_class'=>'w50 m12'),
	'inputType'               => 'checkbox',
	'sql'                     => "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_faq']['fields']['showhelpful'] = array(
	'label'                   => &$GLOBALS['TL_LANG']['tl_faq']['showhelpful'],
	'exclude'                 => true,
	'filter'                  => true,
	'eval'                    => array('tl_class'=>'w50 m12'),
	'inputType'               => 'checkbox',
	'sql'                     => "char(1) NOT NULL default ''"
);
