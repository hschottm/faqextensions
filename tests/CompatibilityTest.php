<?php

namespace Hschottm\FaqExtensionsBundle\Tests;

use Contao\CoreBundle\Framework\ContaoFramework;
use Hschottm\FaqExtensionsBundle\EventListener\InsertTagsListener;
use PHPUnit\Framework\TestCase;

final class CompatibilityTest extends TestCase
{
    public function testAllModulesLoadWithContao53(): void
    {
        foreach (['List', 'TopList', 'RecentList', 'HelpfulList', 'RecommendedList', 'Reader', 'Page'] as $name) {
            self::assertTrue(class_exists('Hschottm\\FaqExtensionsBundle\\ModuleFEFaq'.$name));
        }
    }

    public function testListLinksReceiveTheCurrentFaqModel(): void
    {
        $faq = $this->createMock(\Contao\FaqModel::class);
        $collection = $this->createMock(\Contao\Model\Collection::class);
        $collection->method('count')->willReturn(1);
        $collection->method('next')->willReturnOnConsecutiveCalls(true, false);
        $collection->method('row')->willReturn(['question' => 'Question']);
        $collection->method('current')->willReturn($faq);
        $collection->method('__get')->willReturn('Question');
        $module = $this->getMockBuilder(\Hschottm\FaqExtensionsBundle\ModuleFEFaqList::class)
            ->disableOriginalConstructor()->onlyMethods(['generateFaqLink'])->getMock();
        $module->expects(self::once())->method('generateFaqLink')->with($faq)->willReturn('/faq/answer');
        $method = new \ReflectionMethod($module, 'parseFaq');
        $items = $method->invoke($module, $collection);
        self::assertSame('/faq/answer', $items[0]['href']);
        self::assertSame('first last even', $items[0]['class']);
    }

    public function testFaqOptionsWithoutCommentsBundle(): void
    {
        $GLOBALS['TL_DCA']['tl_faq'] = ['palettes' => ['default' => '{publish_legend},published']];
        require __DIR__.'/../src/Resources/contao/dca/tl_faq.php';
        self::assertSame('{publish_legend},published,recommended,showhelpful', $GLOBALS['TL_DCA']['tl_faq']['palettes']['default']);
        self::assertArrayNotHasKey('noComments', $GLOBALS['TL_DCA']['tl_faq']['fields']);
    }

    public function testMissingInsertTagArgumentDoesNotInitializeFramework(): void
    {
        $framework = $this->createMock(ContaoFramework::class);
        $framework->expects(self::never())->method('initialize');
        $listener = new InsertTagsListener($framework, new \Hschottm\FaqExtensionsBundle\Voting\VoterIdentity('test-secret'));
        self::assertSame('', $listener->onReplaceInsertTags('faq_helpful_url'));
        self::assertFalse($listener->onReplaceInsertTags('unrelated::1'));
    }
}
