<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2015 Leo Feyer
 *
 * @license LGPL-3.0+
 */


/**
 * tl_faq extension
 *
 * @author Helmut Schottmüller <https://github.com/hschottm>
 */

$GLOBALS['TL_DCA']['tl_faq']['palettes']['default'] = str_replace('noComments', 'noComments,recommended', $GLOBALS['TL_DCA']['tl_faq']['palettes']['default']);

$GLOBALS['TL_DCA']['tl_faq']['fields']['viewcount'] = array(
	'sql'                     => "int(10) unsigned NOT NULL default '0'"
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
