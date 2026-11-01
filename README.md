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
as `.html.twig` templates. Create overrides in Contao's template editor as needed.
The four lists extend `faqextensions_list.html.twig`; the reader and FAQ page
share `faqextensions_answer.html.twig` and `faqextensions_vote.html.twig`.
The module template base names remain unchanged.

Under Contao 5.3, the templates use Contao's interoperability layer for the core
module wrappers, images and comment form. No PHP templates are shipped by this
extension.

Custom voting links can use `{{faq_helpful_url::ID_OR_ALIAS}}` and
`{{faq_nothelpful_url::ID_OR_ALIAS}}` on a page containing the corresponding FAQ
reader or FAQ page. Voting must be enabled for the answer.

The existing `tl_faq` fields `viewcount`, `recommended`, `helpful`, `nothelpful`
and `showhelpful` retain their names and stored values.

## Release history

### Twig templates

* Replaced all six bundled PHP templates with Twig templates.
* Shared list, answer and voting markup through reusable Twig templates.
* Preserved module wrappers, pagination, images, enclosures, comments and
  schema.org data, with translated voting controls and explicit insert-tag rendering.
* Added rendering integration tests against a real Contao 5.3 kernel.

### Contao 5.3 support

* Updated dependencies and replaced removed Contao APIs and global class aliases.
* Updated reader routing, preview visibility and pagination error handling.
* Fixed recommendation filtering and the corresponding pagination count.
* Made the backend options available without the optional comments bundle.
* Updated FAQ templates for Contao 5.3, including structured data and comments.
* Preserved existing FAQ content, counters and template names.

## Updating existing installations

Refresh the Contao cache and run database migrations through the Contao Manager
or `php vendor/bin/contao-console contao:migrate` after updating. Convert custom `.html5` overrides to `.html.twig` and review them against the
supplied templates. Existing PHP overrides may still take precedence, so remove
or rename them after converting. The extension does not convert custom templates
automatically. Keep the existing template base names in module settings.

## Development

Install the Composer development dependencies and run `vendor/bin/phpunit`.
The tests cover service registration, list filtering, preview visibility and
pagination counts.

Run the rendering integration tests with an installed Contao 5.3 application
(including the FAQ and comments bundles) and the PHP SQLite extension:

```sh
CONTAO_APP=/path/to/contao php tests/integration/render.php
```

The integration test boots a temporary application with its own SQLite database
and removes it afterwards. It uses the application's installed dependencies but
does not change that application's files or database. It checks all six templates,
custom overrides, escaping, translations, real voting insert tags, images,
downloads, comments and structured data.
