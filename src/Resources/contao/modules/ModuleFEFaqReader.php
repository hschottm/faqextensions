<?php

namespace Hschottm\FaqExtensionsBundle;

use Contao\FaqModel;
use Contao\Input;
use Contao\ModuleFaqReader;

class ModuleFEFaqReader extends ModuleFaqReader
{
    protected function compile()
    {
        $faq = FaqModel::findPublishedByParentAndIdOrAlias(Input::get('auto_item'), $this->faq_categories);
        if ($faq !== null) {
            ++$faq->viewcount;
            $faq->save();
        }

        parent::compile();

        if ($faq !== null) {
            $this->Template->showhelpful = $faq->showhelpful;
            $this->Template->helpful = $faq->helpful;
            $this->Template->nothelpful = $faq->nothelpful;
            $this->Template->alias = $faq->alias;
            $this->Template->faqId = $faq->id;
        }
    }
}
