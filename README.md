# faqextensions
faqextensions extends the FAQ module support in Contao. The extensions allow you to highlight very popular FAQ entries or your personal recommendations in the Contao frontend to make your FAQ section even better.
## Extension of existing Data Container Arrays
### tl_faq
The `tl_faq` dca has been extended with two new database fields.
#### viewcount
`viewcount` adds an integer counter to each FAQ entry. Each time an entry will be viewed in the frontend, the counter is increased by 1 containing the abolute number of views for this FAQ entry. A new module *Most frequently viewed FAQ list* lists FAQ entries ordered by the number of their absolute view count to present the most viewed FAQ entries.
```php
$GLOBALS['TL_DCA']['tl_faq']['fields']['viewcount'] = array(
	'sql'                     => "int(10) unsigned NOT NULL default '0'"
);
```
#### recommended
`recommended` is a boolean field that can be checked in the backend for each FAQ entry to mark it as *recommended* or *important*. A new module *Recommended FAQ list* lists all FAQ entries which are marked as recommended.
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
![recommended_backend](https://cloud.githubusercontent.com/assets/873113/12032366/da35e33c-ae17-11e5-8071-535cd6ed054e.png)

## Modules
### Most frequently viewed FAQ list
This module creates a list of the most frequently viewed FAQ entries. Only FAQ entries with a `viewcount`greater 0 will be used to generate the list. You can limit the number of entries by entering an upper limit. The order of the FAQ entries is always descending by `viewcount` so the most fequently viewed entry is on top of the list.

![most_frequent_module](https://cloud.githubusercontent.com/assets/873113/12032443/a51d6e44-ae18-11e5-9117-1eebd1e4a242.png)
### Recommended FAQ list
This module creates a list of all FAQ entries marked as recommended. You can limit the number of list entries by entering an upper limit and you can set the sorter order for the entries to alphabetical by FAQ title or by creation date of the FAQ entry, both ascending and descending.

![recommended_module](https://cloud.githubusercontent.com/assets/873113/12032444/a51f9926-ae18-11e5-92b5-58f345e2a0df.png)

