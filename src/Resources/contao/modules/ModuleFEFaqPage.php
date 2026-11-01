<?php

namespace Hschottm\FaqExtensionsBundle;

use Contao\FaqModel;
use Contao\ModuleFaqPage;

class ModuleFEFaqPage extends ModuleFaqPage
{
    protected function compile()
    {
        $faqs = FaqModel::findPublishedByPids($this->faq_categories);
        if ($faqs !== null) {
            while ($faqs->next()) {
                ++$faqs->viewcount;
                $faqs->save();
            }
        }

        parent::compile();

        // Expose each legacy stdClass item as a Twig include context.
        $categories = $this->Template->faq ?? [];
        foreach ($categories as &$category) {
            $category['items'] = array_map(static fn ($item) => (array) $item, $category['items']);
        }
        unset($category);
        $this->Template->faq = $categories;
    }
}
