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
* Backend labels, help texts and voting messages are available in 18 languages.

## Languages

Complete translations cover all 16 languages shipped with Contao 5.3 Core:
Czech (`cs`), German (`de`), English (`en`), Spanish (`es`), Persian (`fa`),
French (`fr`), Italian (`it`), Japanese (`ja`), Dutch (`nl`), Polish (`pl`),
Russian (`ru`), Slovak (`sk`), Slovenian (`sl`), Serbian (`sr`, Cyrillic),
Swedish (`sv`) and Turkish (`tr`). Latvian (`lv`) and Portuguese (`pt`), which
are also shipped with Contao's FAQ bundle, are included as well.

The scope follows the language directories of the official
[Contao 5.3 Core](https://github.com/contao/contao/tree/5.3/core-bundle/contao/languages)
and [FAQ bundle](https://github.com/contao/contao/tree/5.3/faq-bundle/contao/languages).
Contao's frontend allows additional languages; those do not all have official
Core translations.

Regional locale variants such as `de-CH`, `fr-CA` and `pt-BR` use the base-language
catalogue through Symfony's locale fallback. Serbian uses the supplied Cyrillic
translation; script variants are not transliterated. Persian voting pages use
right-to-left text direction. English remains the fallback for unsupported
languages. The backend language follows the user's Contao preference; voting
links use the language of the page they appear on.

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

## Preventing repeated ratings

Voting links open a confirmation page. A rating is counted only after a
CSRF-protected POST, so prefetching a link or a crawler visiting it does not vote.
The confirmation page is never cached. The first submitted vote is final;
submitting again, including the opposite rating, does not change the counters.

* Anonymous visitors are recognized by a signed, random browser cookie named
  `faqextensions_voter`, valid for one year from the last voting-page visit.
* Logged-in frontend members are additionally recognized by their member account,
  including when they use another browser. Browser and account claims are linked
  when a rating is submitted, preventing a second vote through logging in or out.
* A transaction and a unique database constraint protect the counters against
  duplicate and concurrent requests. Existing aggregate vote counts are retained.
* Only keyed hashes of browser/account identifiers and a timestamp are stored in
  `tl_faq_vote`. The extension does not store IP addresses or browser fingerprints.
  Claims are deleted with the FAQ and are not copied when an FAQ is duplicated.

Cookies must be enabled to vote. This is a practical deterrent, not proof of a
person's identity: anonymous visitors can vote again after clearing/expiring their
cookie or switching browsers. Different people sharing the same browser may be
prevented from voting separately. Votes collected before this update cannot be
associated with voters retrospectively. Rotating the application secret also
invalidates existing cookie identities and signed voting links.

The cookie and stored identifiers should be described in the site's privacy
information. The cookie is first issued when the visitor opens a voting link;
ordinary FAQ page views do not set this cookie.

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
reader or FAQ page. Voting must be enabled and the answer must be published.
These insert tags now generate signed links to the confirmation page. Old manually
constructed `?vote=...&tl_faq=...` links no longer submit votes; replace them with
the insert tags.

The existing `tl_faq` fields `viewcount`, `recommended`, `helpful`, `nothelpful`
and `showhelpful` retain their names and stored values.

## Release history

### Translations

* Added complete catalogues for all 18 languages shipped with Contao 5.3 Core
  and the FAQ bundle, including the voting confirmation and error messages.
* Added locale normalization, regional fallback and Persian right-to-left output.
* Corrected the list descriptions and sort labels to refer to the last modification.

### Protection against repeated ratings

* Added browser and member recognition with transactional vote recording.
* Added signed confirmation links, CSRF protection and an uncached voting page.
* Added the `tl_faq_vote` table, preserving existing aggregate counts.
* Invalidate FAQ and category cache tags after accepted votes.

### FAQ duplication

* Reset helpful and unhelpful vote counters to zero when duplicating an FAQ.

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

Run the database migration to create `tl_faq_vote` **before accepting new votes**,
and clear the application/page caches to replace old voting links. FAQ content
and existing vote totals are preserved.

Refresh the Contao cache and run database migrations through the Contao Manager
or `php vendor/bin/contao-console contao:migrate` after updating. Convert custom `.html5` overrides to `.html.twig` and review them against the
supplied templates. Existing PHP overrides may still take precedence, so remove
or rename them after converting. The extension does not convert custom templates
automatically. Keep the existing template base names in module settings.

## Development

Install the Composer development dependencies and run `vendor/bin/phpunit`.
The tests cover service registration, list filtering, preview visibility and
pagination counts, duplicate voting, rollback on errors and concurrent requests.
The voting tests require PDO SQLite; the concurrency test additionally needs `pcntl`.
Translation tests verify catalogue completeness, UTF-8 strings and numeric placeholders.

Run the rendering integration tests with an installed Contao 5.3 application
(including the FAQ and comments bundles) and the PHP SQLite extension:

```sh
CONTAO_APP=/path/to/contao php tests/integration/render.php
```

The integration test boots a temporary application with its own SQLite database
and removes it afterwards. It uses the application's installed dependencies but
does not change that application's files or database. It checks all six templates,
custom overrides, escaping, translations, real voting insert tags, images,
downloads, comments, structured data and the full HTTP confirmation/POST flow.
All 18 languages, regional fallbacks and Persian text direction are checked with
the real Contao translation loader and HTTP routes.
