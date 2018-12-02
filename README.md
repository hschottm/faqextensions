[![Latest Version on Packagist](http://img.shields.io/packagist/v/hschottm/faqextensions.svg?style=flat)](https://packagist.org/packages/hschottm/faqextensions)
[![Installations via composer per month](http://img.shields.io/packagist/dm/hschottm/faqextensions.svg?style=flat)](https://packagist.org/packages/hschottm/faqextensions)
[![Installations via composer total](http://img.shields.io/packagist/dt/hschottm/faqextensions.svg?style=flat)](https://packagist.org/packages/hschottm/faqextensions)


# faqextensions
faqextensions extends the FAQ bundle support in Contao. The extensions allow you to highlight very popular FAQ entries or your personal recommendations in the Contao frontend to make your FAQ section even better. This Contao bundle brings you the following improvements to the FAQ bundle:

* FAQ list module: Most viewed - lists your FAQs by the most viewed FAQ articles, includes optional pagination
* FAQ list module: Most recent - lists your FAQs by the most recent added FAQ articles, includes optional pagination
* FAQ list module: Recommended - lists all your recommended FAQ articles, includes optional pagination and custom sort orders
* FAQ list module: Most helpful - lists your FAQs by the most helpful articles based on a helpful vote by the users, includes optional pagination

The faqextensions bundle adds a `viewcount` counter for each FAQ that increases with every view in the frontend. Each FAQ article gets a `Recommended` checkbox to make the article as recommended in the backend. Each FAQ article gets a `Show helpful` checkbox to show the helpful vote at the end of an article in the frontend to allow the users to vote an article up or down.

## Extension of existing Data Container Arrays
### tl_faq
The `tl_faq` dca has been extended with some new database fields.
#### viewcount
`viewcount` adds an integer counter to each FAQ article. Each time an entry will be viewed in the frontend, the counter is increased by 1 containing the abolute number of views for this FAQ article. A new module *Most frequently viewed FAQ list* lists FAQ entries ordered by the number of their absolute view count to present the most viewed FAQ entries.
```php
$GLOBALS['TL_DCA']['tl_faq']['fields']['viewcount'] = array(
	'sql'                     => "int(10) unsigned NOT NULL default '0'"
);
```
#### recommended
`recommended` is a boolean field that can be checked in the backend for each FAQ article to mark it as *recommended* or *important*. A new module *Recommended FAQ list* lists all FAQ articles which are marked as recommended.
```php
$GLOBALS['TL_DCA']['tl_faq']['fields']['recommended'] = array(
	'label'                   => &$GLOBALS['TL_LANG']['tl_faq']['recommended'],
	'exclude'                 => true,
	'filter'                  => true,
	'eval'                    => array('tl_class'=>'w50 m12'),
	'inputType'               => 'checkbox',
	'sql'                     => "char(1) NOT NULL default ''"
);
```
![faqextensions_expert_settings](https://user-images.githubusercontent.com/873113/48935073-a01e9080-ef06-11e8-83dd-26d129117d02.png)

#### helpful, nothelpful, showhelpful
`showhelpful` is a boolean field that can be checked in the backend for each FAQ article to show a helpful vote in the frontend for that article. Each helpful vote will increase the integer value of the `helpful` integer field, each not helpful vorte will increase the value of the `nothelpful`integer field. The module *Helpful FAQ list* lists all FAQ entries ordered by their helpful votes.

```php
$GLOBALS['TL_DCA']['tl_faq']['fields']['helpful'] = array(
	'sql'                     => "int(10) unsigned NOT NULL default '0'"
);

$GLOBALS['TL_DCA']['tl_faq']['fields']['nothelpful'] = array(
	'sql'                     => "int(10) unsigned NOT NULL default '0'"
);

$GLOBALS['TL_DCA']['tl_faq']['fields']['showhelpful'] = array(
	'label'                   => &$GLOBALS['TL_LANG']['tl_faq']['showhelpful'],
	'exclude'                 => true,
	'filter'                  => true,
	'eval'                    => array('tl_class'=>'w50 m12'),
	'inputType'               => 'checkbox',
	'sql'                     => "char(1) NOT NULL default ''"
);
```

![faqextensions_expert_settings](https://user-images.githubusercontent.com/873113/48935073-a01e9080-ef06-11e8-83dd-26d129117d02.png)

## Modules
### Most frequently viewed FAQ list
This module creates a list of the most frequently viewed FAQ articles ordered by their view count. You can limit the number of entries and use pagination for this list. The order of the FAQ articles is always descending by `viewcount` so the most fequently viewed entry is on top of the list.

![faqexentensions_mostfrequent_list](https://user-images.githubusercontent.com/873113/48936106-1ec8fd00-ef0a-11e8-9ece-c2246dbc9d5e.png)
### Recommended FAQ list
This module creates a list of all FAQ articles marked as recommended. You can limit the number of entries and use pagination for this list and you can set the sorter order for the entries to alphabetical by FAQ title or by creation date of the FAQ entry, both ascending and descending.

![faqextensions_recommended_list](https://user-images.githubusercontent.com/873113/48936109-1f619380-ef0a-11e8-9307-b373a2acfd92.png)
### Helpful FAQ list
This module creates a list of the most helpful FAQ articles, odered by their helpful vote. You can limit the number of entries and use pagination for this list. The order of the FAQ articles is always descending by their helpfulness calculated by the sum of the helpful and not helpful votes. The not helpful votes count as negative values.

![faqextensions_helpful_list](https://user-images.githubusercontent.com/873113/48936107-1ec8fd00-ef0a-11e8-86e0-5cde37ed8ff5.png)
### Recent FAQ list
This module creates a list of all FAQ articles ordered descending by the most recently added article using the `tstamp` field of each article. You can limit the number of entries and use pagination for this list.

![faqextensions_recent_list](https://user-images.githubusercontent.com/873113/48936108-1ec8fd00-ef0a-11e8-8684-8194f70791e4.png)
