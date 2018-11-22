<?php

 namespace Hschottm\FaqExtensionsBundle;

class FEFaqModel extends \Contao\FaqModel
{
  /**
	 * Count published faq articles by their parent ID
	 *
	 * @param array   $arrPids     An array of faq archive IDs
	 * @param array   $arrOptions  An optional options array
	 *
	 * @return integer The number of news items
	 */
	public static function countPublishedByPids($arrPids, array $arrOptions=array())
	{
		if (empty($arrPids) || !\is_array($arrPids))
		{
			return 0;
		}

		$t = static::$strTable;
		$arrColumns = array("$t.pid IN(" . implode(',', array_map('\intval', $arrPids)) . ")");

    if (!static::isPreviewMode($arrOptions))
		{
			$arrColumns[] = "$t.published='1'";
		}

		return static::countBy($arrColumns, null, $arrOptions);
	}

	/**
	 * Find all published FAQs by their view count
	 *
   * @param array   $arrPids     An array of faq archive IDs
	 * @param integer $intLimit    An optional limit
	 * @param integer $intOffset   An optional offset
	 *
	 * @return \Model\Collection|\FaqModel|null A collection of models or null if there are no FAQs
	 */

	public static function findPublishedByViewCountAndPids($arrPids, $intLimit=0, $intOffset=0)
	{
		$t = static::$strTable;
		$arrColumns = array("$t.pid IN(" . implode(',', array_map('\intval', $arrPids)) . ")");

		if (!BE_USER_LOGGED_IN)
		{
			$arrColumns[] = "$t.published='1'";
		}

    $arrOptions['limit']  = $intLimit;
		$arrOptions['offset'] = $intOffset;
		$arrOptions['order'] = "$t.viewcount DESC";

		return static::findBy($arrColumns, null, $arrOptions);
	}


	/**
	 * Find all published FAQs by their recommendation flag
	 *
	 * @param array $arrOptions An optional options array
	 *
	 * @return \Model\Collection|\FaqModel|null A collection of models or null if there are no FAQs
	 */

	public static function findPublishedByRecommendation(array $arrOptions=array())
	{
		$t = static::$strTable;
		$arrColumns = array("$t.recommended=?");

		if (!BE_USER_LOGGED_IN)
		{
			$arrColumns[] = "$t.published='1'";
		}

		if (strcmp($arrOptions['order'], 'sort_alpha_desc') == 0)
		{
			$arrOptions['order'] = "$t.question DESC";
		}
		else if (strcmp($arrOptions['order'], 'sort_alpha_asc') == 0)
		{
			$arrOptions['order'] = "$t.question ASC";
		}
		else if (strcmp($arrOptions['order'], 'sort_created_desc') == 0)
		{
			$arrOptions['order'] = "$t.tstamp DESC";
		}
		else if (strcmp($arrOptions['order'], 'sort_created_asc') == 0)
		{
			$arrOptions['order'] = "$t.tstamp ASC";
		}
		else
		{
			$arrOptions['order'] = "$t.question DESC";
		}

		return static::findBy($arrColumns, 1, $arrOptions);
	}

  /**
	 * Find all published FAQs by their helpful votes
	 *
	 * @param array $arrOptions An optional options array
	 *
	 * @return \Model\Collection|\FaqModel|null A collection of models or null if there are no FAQs
	 */

	public static function findPublishedByHelpful(array $arrOptions=array())
	{
		$t = static::$strTable;
		$arrColumns = array("($t.helpful>0 OR $t.nothelpful>0)");

		if (!BE_USER_LOGGED_IN)
		{
			$arrColumns[] = "$t.published='1'";
		}

    $arrOptions['order'] = "($t.helpful-$t.nothelpful) DESC";


		return static::findBy($arrColumns, 0, $arrOptions);
	}

  /**
	 * Find all published FAQs by their creation date
	 *
	 * @param array $arrOptions An optional options array
	 *
	 * @return \Model\Collection|\FaqModel|null A collection of models or null if there are no FAQs
	 */

	public static function findPublishedByDate(array $arrOptions=array())
	{
		$t = static::$strTable;
		$arrColumns = array();

		if (!BE_USER_LOGGED_IN)
		{
			$arrColumns[] = "$t.published='1'";
		}

    $arrOptions['order'] = "$t.tstamp DESC";


		return static::findBy($arrColumns, 0, $arrOptions);
	}
}
