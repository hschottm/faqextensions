<?php

$GLOBALS['FE_MOD']['faq']['faqpage'] = 'Hschottm\FaqExtensionsBundle\ModuleFEFaqPage';
$GLOBALS['FE_MOD']['faq']['faqreader'] = 'Hschottm\FaqExtensionsBundle\ModuleFEFaqReader';
$GLOBALS['FE_MOD']['faq']['faqtoplist'] = 'Hschottm\FaqExtensionsBundle\ModuleFEFaqTopList';
$GLOBALS['FE_MOD']['faq']['faqrecommendedlist'] = 'Hschottm\FaqExtensionsBundle\ModuleFEFaqRecommendedList';
$GLOBALS['FE_MOD']['faq']['faqhelpfullist'] = 'Hschottm\FaqExtensionsBundle\ModuleFEFaqHelpfulList';
$GLOBALS['FE_MOD']['faq']['faqrecentlist'] = 'Hschottm\FaqExtensionsBundle\ModuleFEFaqRecentList';

$GLOBALS['TL_HOOKS']['replaceInsertTags'][] = array('contao_faqextensions.listener.insert_tags', 'onReplaceInsertTags');
