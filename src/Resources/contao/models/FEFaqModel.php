<?php

 namespace Hschottm\FaqExtensionsBundle;

class FEFaqModel extends \Contao\FaqModel
{

	/**
	 * Find all published FAQs by their view count. view count must be greater 0
	 *
	 * @param array $arrOptions An optional options array
	 *
	 * @return \Model\Collection|\FaqModel|null A collection of models or null if there are no FAQs
	 */

	public static function findPublishedByViewCount(array $arrOptions=array())
	{
		$t = static::$strTable;
		$arrColumns = array("$t.viewcount>?");

		if (!BE_USER_LOGGED_IN)
		{
			$arrColumns[] = "$t.published='1'";
		}

		$arrOptions['order'] = "$t.viewcount DESC";

		return static::findBy($arrColumns, 0, $arrOptions);
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
