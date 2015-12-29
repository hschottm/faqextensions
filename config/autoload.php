<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2015 Leo Feyer
 *
 * @license LGPL-3.0+
 */


/**
 * Register the namespaces
 */
ClassLoader::addNamespaces(array
(
	'FaqExtensions',
));


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Models
	'FaqExtensions\FaqModel'                 => 'system/modules/faqextensions/models/FaqModel.php',

	// Modules
	'FaqExtensions\ModuleFaqPage'            => 'system/modules/faqextensions/modules/ModuleFaqPage.php',
	'FaqExtensions\ModuleFaqReader'          => 'system/modules/faqextensions/modules/ModuleFaqReader.php',
	'FaqExtensions\ModuleFaqRecommendedList' => 'system/modules/faqextensions/modules/ModuleFaqRecommendedList.php',
	'FaqExtensions\ModuleFaqTopList'         => 'system/modules/faqextensions/modules/ModuleFaqTopList.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'mod_faqlist_top' => 'system/modules/faqextensions/templates/modules',
));
