# Changelog

Changes are grouped by release line, newest version first within each line.
This repository-wide history includes all release tags; entries for other lines
do not change the compatibility of the installed package. Historical entries
were reconstructed from tag diffs and the former README release history.

## 5.1.x

### [5.1.2] - 2026-11-10

- Moved release history from the README to a complete changelog with dated entries and links for every tag.
- Retained usage, migration and development instructions in the README; no runtime or dependency changes.

### [5.1.1] - 2026-11-10

- Added the shared SVG package icon, README preview and Contao Manager logo documentation.

### [5.1.0] - 2026-11-01

* Require Contao 5.7 and PHP 8.3 or newer.
* Retain Twig templates, translations and protected FAQ voting.
* Verified with Contao 5.7.15: 15 automated tests and 357 integration assertions.

## 5.0.x

### [5.0.2] - 2026-11-10

- Moved release history from the README to the same repository-wide changelog used by the main branch.
- Retained usage, migration and development instructions in the README; no runtime or dependency changes.

### [5.0.1] - 2026-11-10

- Added the shared SVG package icon, README preview and Contao Manager logo documentation.

### [5.0.0] - 2026-11-01

#### Translations

* Added complete catalogues for all 18 languages shipped with Contao 5.3 Core
  and the FAQ bundle, including the voting confirmation and error messages.
* Added locale normalization, regional fallback and Persian right-to-left output.
* Corrected the list descriptions and sort labels to refer to the last modification.

#### Protection against repeated ratings

* Added browser and member recognition with transactional vote recording.
* Added signed confirmation links, CSRF protection and an uncached voting page.
* Added the `tl_faq_vote` table, preserving existing aggregate counts.
* Invalidate FAQ and category cache tags after accepted votes.

#### FAQ duplication

* Reset helpful and unhelpful vote counters to zero when duplicating an FAQ.

#### Twig templates

* Replaced all six bundled PHP templates with Twig templates.
* Shared list, answer and voting markup through reusable Twig templates.
* Preserved module wrappers, pagination, images, enclosures, comments and
  schema.org data, with translated voting controls and explicit insert-tag rendering.
* Added rendering integration tests against a real Contao 5.3 kernel.

#### Contao 5.3 support

* Updated dependencies and replaced removed Contao APIs and global class aliases.
* Updated reader routing, preview visibility and pagination error handling.
* Fixed recommendation filtering and the corresponding pagination count.
* Made the backend options available without the optional comments bundle.
* Updated FAQ templates for Contao 5.3, including structured data and comments.
* Preserved existing FAQ content, counters and template names.

#### License and compatibility

- Standardized licensing on LGPL-3.0-or-later.
- Updated the Contao Manager plugin method signature.

## 4.6.x

### [4.6.1] - 2026-11-10

- Added the shared SVG package icon, README preview and Contao Manager logo documentation.

### [4.6.0] - 2022-07-24

- Updated Composer requirements for Contao 4.13, PHP 7.4/PHP 8 and compatible development tools.

## 4.4.x

### [4.4.4] - 2026-11-10

- Added the shared SVG package icon, README preview and Contao Manager logo documentation.
- Based on the `Contao-4.4` branch at `4.4.0`, rather than the later `4.4.3` snapshot; the intervening 4.4.1–4.4.3 changes are not part of this tag.

### [4.4.3] - 2020-06-19

- Removed the explicit Symfony FrameworkBundle dependency and refreshed development dependencies.

### [4.4.2] - 2019-03-01

- Added `rel="nofollow"` to voting links to discourage crawler-triggered votes.
- Changed helpful/unhelpful counter fields from unsigned integers to signed integers.
- Updated the package description and README.

### [4.4.1] - 2018-11-24

- Fixed a syntax error in the recommended FAQ list sort-order parameters.

### [4.4.0] - 2018-11-23

- Released the Contao 4.4 bundle with Composer and Contao Manager integration.
- Added helpful/unhelpful voting, helpful FAQ lists and recent FAQ lists.
- Added list limits, pagination, sorting options and backend controls; updated templates and documentation.

## 1.0.x

### [1.0.1] - 2026-11-10

- Added the shared SVG package icon, README preview and Contao Manager logo documentation.
- Asset/documentation update for the legacy `contao2` branch.

### [1.0] - 2015-12-29

- Initial tagged release of the legacy FAQ extension.
- Added view counters, recommendation flags, most-viewed and recommended FAQ lists.

[5.1.2]: https://github.com/hschottm/faqextensions/releases/tag/5.1.2
[5.1.1]: https://github.com/hschottm/faqextensions/releases/tag/5.1.1
[5.1.0]: https://github.com/hschottm/faqextensions/releases/tag/5.1.0
[5.0.2]: https://github.com/hschottm/faqextensions/releases/tag/5.0.2
[5.0.1]: https://github.com/hschottm/faqextensions/releases/tag/5.0.1
[5.0.0]: https://github.com/hschottm/faqextensions/releases/tag/5.0.0
[4.6.1]: https://github.com/hschottm/faqextensions/releases/tag/4.6.1
[4.6.0]: https://github.com/hschottm/faqextensions/releases/tag/4.6.0
[4.4.4]: https://github.com/hschottm/faqextensions/releases/tag/4.4.4
[4.4.3]: https://github.com/hschottm/faqextensions/releases/tag/4.4.3
[4.4.2]: https://github.com/hschottm/faqextensions/releases/tag/4.4.2
[4.4.1]: https://github.com/hschottm/faqextensions/releases/tag/4.4.1
[4.4.0]: https://github.com/hschottm/faqextensions/releases/tag/4.4.0
[1.0.1]: https://github.com/hschottm/faqextensions/releases/tag/1.0.1
[1.0]: https://github.com/hschottm/faqextensions/releases/tag/1.0
