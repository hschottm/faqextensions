<?php

\Contao\CoreBundle\DataContainer\PaletteManipulator::create()
    ->addField(['recommended', 'showhelpful'], 'publish_legend', \Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('default', 'tl_faq');

$GLOBALS['TL_DCA']['tl_faq']['fields']['viewcount'] = array(
	'sql'                     => "int(10) unsigned NOT NULL default '0'"
);

$GLOBALS['TL_DCA']['tl_faq']['fields']['helpful'] = array(
	'default'                 => 0,
	'eval'                    => array('doNotCopy' => true),
	'sql'                     => "int(10) NOT NULL default '0'"
);

$GLOBALS['TL_DCA']['tl_faq']['fields']['nothelpful'] = array(
	'default'                 => 0,
	'eval'                    => array('doNotCopy' => true),
	'sql'                     => "int(10) NOT NULL default '0'"
);



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
