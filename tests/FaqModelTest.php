<?php

namespace Hschottm\FaqExtensionsBundle\Tests;

use Hschottm\FaqExtensionsBundle\FEFaqModel;
use PHPUnit\Framework\TestCase;

final class FaqModelTest extends TestCase
{
    public function testRecommendationsFilterBothResultsAndPagination(): void
    {
        QueryFaqModel::findPublishedByRecommendationAndPids([2], 10, 5);
        self::assertContains('tl_faq.recommended=1', QueryFaqModel::$query[0]);
        self::assertContains("tl_faq.published='1'", QueryFaqModel::$query[0]);
        self::assertSame(10, QueryFaqModel::$query[1]['limit']);
        self::assertSame(5, QueryFaqModel::$query[1]['offset']);
        self::assertSame('tl_faq.question ASC', QueryFaqModel::$query[1]['order']);
        QueryFaqModel::countPublishedByPids([2], [], true);
        self::assertContains('tl_faq.recommended=1', QueryFaqModel::$query[0]);
    }

    public function testEveryListHonoursPreviewAndEmptyCategories(): void
    {
        foreach (['ViewCount', 'Helpful', 'Date', 'Recommendation'] as $sort) {
            $method = 'findPublishedBy'.$sort.'AndPids';
            self::assertNull(QueryFaqModel::$method([]));
            QueryFaqModel::$method([2], 0, 0, ['preview' => false]);
            self::assertContains("tl_faq.published='1'", QueryFaqModel::$query[0]);
            QueryFaqModel::$method([2], 0, 0, ['preview' => true]);
            self::assertNotContains("tl_faq.published='1'", QueryFaqModel::$query[0]);
        }
        self::assertSame(0, QueryFaqModel::countPublishedByPids([]));
    }
}

class QueryFaqModel extends FEFaqModel
{
    public static array $query = [];

    protected static function isPreviewMode(array $arrOptions)
    {
        return $arrOptions['preview'] ?? false;
    }

    public static function findBy($strColumn, $varValue, array $arrOptions = [])
    {
        self::$query = [$strColumn, $arrOptions];
        return null;
    }

    public static function countBy($strColumn = null, $varValue = null, array $arrOptions = [])
    {
        self::$query = [$strColumn, $arrOptions];
        return 0;
    }
}
