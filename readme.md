MultiEdit for Wolf CMS
======================

Plugin for editing multiple pages interactively.

This plugin helps you to edit multiple pages based on jQuery, so you don't have to wait for page reload and click "Save and continue editing" to see the changes.  All changes are made (almost) instantly.

You can also edit page parts but only those *without filter* applied. Page parts with filters are listed below each page. If you have Part Revisions plugin enabled, the changes in page part contents will be stored as a revision.

Installation
------------

MultiEdit Plugin can be installed into your WolfCMS by uploading it to ***CMS_ROOT/wolf/plugins/multiedit/*** and enabling it in administration panel.

Changelog
---------

0.0.7

- fixed "Show subpages" list to always show full pages hierarchy
- valid_until set in past makes page status "Archived"
- added "default" sorting option (as in Wolf's Pages tab)
- Part Revisions plugin compatible
- collapsible items
- improved messages and I18n
- sticky message box (scrolling with view)
- minor bugfixes
- minor visual enhancements

0.0.6

- preloader
- root page protection (slug, status)
- color page status indication
- visual improvements (alts, titles)
- browser side performance optimizations
- minor bugfixes