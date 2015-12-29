# faqextensions
faqextensions extends the FAQ module support in Contao.
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
### Recommended FAQ list
