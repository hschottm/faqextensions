[![Latest Version on Packagist](https://img.shields.io/packagist/v/hschottm/faqextensions.svg?style=flat)](https://packagist.org/packages/hschottm/faqextensions)
[![Installations via composer per month](https://img.shields.io/packagist/dm/hschottm/faqextensions.svg?style=flat)](https://packagist.org/packages/hschottm/faqextensions)
[![Installations via composer total](https://img.shields.io/packagist/dt/hschottm/faqextensions.svg?style=flat)](https://packagist.org/packages/hschottm/faqextensions)

# faqextensions

faqextensions extends Contao's FAQ bundle with popular, recommended and helpful
FAQ lists. Visitors can rate answers, and editors can highlight recommendations.

## Features

* **Most frequently viewed FAQ list:** sorts answers by their view counter.
* **Recent FAQ list:** sorts answers by their last modification (`tstamp`).
* **Recommended FAQ list:** shows only recommended answers, sorted by question
  or modification date in ascending or descending order.
* **Helpful FAQ list:** sorts answers by helpful votes minus unhelpful votes.
* All four lists support category selection, an item limit, pagination and an
  optional FAQ reader module.
* The standard FAQ reader and FAQ page display voting links when enabled.
* German and English backend labels and voting messages are included.

## Requirements

* Contao 5.3 or newer within Contao 5 (`^5.3`)
* PHP 8.1 or newer
* Contao FAQ bundle

The Contao comments bundle is optional.

## Installation

Install `hschottm/faqextensions` through the Contao Manager or Composer, then run
the database migrations:

```sh
composer require hschottm/faqextensions
php vendor/bin/contao-console contao:migrate
```

Until a compatible version is tagged, select `dev-master` explicitly when
installing this development version.

## Usage

1. Create FAQ categories and answers in the Contao backend.
2. Enable **Recommended** for answers you want to highlight, or **Show helpful**
   to display the voting controls.
3. Create one of the additional FAQ list modules, select its categories and
   configure the item limit and pagination.
4. Set the categories' redirect pages to pages containing a FAQ reader, or select
   a reader module in the list to display the answer on the same page.
5. Include the module in your page layout or as a frontend module content element.

Views are counted when the FAQ reader or FAQ page executes. Responses served
from the page cache do not execute PHP and therefore do not increment counters.
Votes are simple feedback counters; repeat votes are not prevented.

## Templates and insert tags

The extension supplies `mod_faqtoplist`, `mod_faqrecentlist`,
`mod_faqrecommendedlist`, `mod_faqhelpfullist`, `mod_faqreader` and `mod_faqpage`
as `.html5` templates. Create overrides in Contao's template editor as needed.

Custom voting links can use `{{faq_helpful_url::ID_OR_ALIAS}}` and
`{{faq_nothelpful_url::ID_OR_ALIAS}}` on a page containing the corresponding FAQ
reader or FAQ page. Voting must be enabled for the answer.

The existing `tl_faq` fields `viewcount`, `recommended`, `helpful`, `nothelpful`
and `showhelpful` retain their names and stored values.

## Release history

### Contao 5.3 support

* Updated dependencies and replaced removed Contao APIs and global class aliases.
* Updated reader routing, preview visibility and pagination error handling.
* Fixed recommendation filtering and the corresponding pagination count.
* Made the backend options available without the optional comments bundle.
* Updated FAQ templates for Contao 5.3, including structured data and comments.
* Preserved existing FAQ content, counters and template names.

## Updating existing installations

Refresh the Contao cache and run database migrations through the Contao Manager
or `php vendor/bin/contao-console contao:migrate` after updating. Review custom
FAQ reader/page template overrides against the supplied templates to retain
voting controls and Contao's current rendering features.

## Development

Install the Composer development dependencies and run `vendor/bin/phpunit`.
The tests cover service registration, list filtering, preview visibility and
pagination counts.
