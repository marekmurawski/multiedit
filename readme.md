MultiEdit for Wolf CMS
======================

Plugin for editing multiple pages interactively.

This plugin helps you to edit multiple pages based on jQuery, so you don't have to wait for page reload and click "Save and continue editing" to see the changes.  All changes are made (almost) instantly. If you want you can also edit pages' metadata and parts in **frontend**.

MultiEdit provides page parts editing and changing names of existing page parts. If you have Part Revisions plugin enabled, the changes in page part contents will be stored as a revision.

Installation
------------

MultiEdit Plugin can be installed into your WolfCMS by uploading it to ***CMS_ROOT/wolf/plugins/multiedit/*** and enabling it in administration panel.

Changelog
---------

##### 0.3.1

- Wolf CMS 0.7.6 support
- i18n update for 0.7.6 support


##### 0.3.0

- new **tabbed page parts display** in frontend and backend
  - colored tabs for `none`, `ace`, `tinymce`, `markdown` and `textile` filters
  - configurable editor height
- full **page part manipulation**, now you can do whatever you want with
  page parts: **add, rename, delete**
- **all page parts** are now **editable** in MultiEdit regardless of filter
- **Ace Syntax Highlighter support** in parts editing *(requires Ace plugin v.0.1.0)*
- **markdown and textile** filter syntax highlighting autodetection in Ace
  *I highly recommend using MultiEdit together with ACE. This combo gives
  you really powerful tool for quick and effective editing your site.*
- **Restrict PHP** plugin compatibility
- **Part Revisions** plugin compatibility
- active page part is remembered in frontend
- removed <del>tags_input</del> support
- removed page part <del>autosizing</del> option

##### 0.2.3

- modifying frontend `jQuery.live()` to `jQuery.delegate()`  calls in order to support newer jQuery versions

##### 0.2.2

- **add / delete / rename** extended fields _(columns)_ of Page model _(mySQL)_
- **add / delete** extended fields _(columns)_ of Page model _(SQLite)_

##### 0.2.0

- editing **extended page fields** - like _comment status_ from Comments plugin
- option to **autosize** page part contents to fit contents into textarea
- new view settings to **customize visible fields**
- option to **rename page parts** _(click part name label)_
- highlighting filters without proper plugin activated
- page parts with _ace, codemirror, markdown and textile_ filters are editable by default
- new icons and some visual improvements
- minor bugfixes
- **new Role - Multieditor**
    This role gives users full MultiEdit access.  
    You can assign roles to users in Wolf CMS Users tab.
- **new Permissions introduced**
  - **multiedit_view** - tab access in backend
  - **multiedit_basic** - view/edit basic page fields
  - **multiedit_advanced** - view/edit extended (plugin-provided) page fields
  - **multiedit_parts** - view/edit page parts
  - **multiedit_frontend** - frontend access to MultiEdit

By default role "Editor" is granted `multiedit_view`,
`multiedit_basic` and `multiedit_frontend` permissions.

Role "Developer" is granted `multiedit_view`,
`multiedit_basic`, `multiedit_parts`
and `multiedit_frontend` permissions.

Permissions and Roles can be manipulated
using **Roles Manager** plugin by **andrewmman**.

##### 0.1.1

- frontend - tags_input plugin integration
- frontend - panel trigger button
- frontend - display settings stored in session cookies
- backend option to show ALL pages of website in one list
- documentation with Gist

##### 0.1.0

- bugfix: variable notice

##### 0.0.9

- FRONTEND EDITING support
- color character counters indicating short meta descriptions/titles

##### 0.0.8

- translation for polish language

##### 0.0.7

- fixed "Show subpages" list to always show full pages hierarchy
- valid_until set in past makes page status "Archived"
- added "default" sorting option (as in Wolf's Pages tab)
- Part Revisions plugin compatible
- collapsible items
- improved messages and I18n
- sticky message box (scrolling with view)
- minor bugfixes
- minor visual enhancements

##### 0.0.6

- preloader
- root page protection (slug, status)
- color page status indication
- visual improvements (alts, titles)
- browser side performance optimizations
- minor bugfixes

License
-------

* GPLv3 license

Disclaimer
----------

While I make every effort to deliver quality plugins for Wolf CMS, I do not guarantee that they are free from defects. They are provided “as is," and you use it at your own risk. I'll be happy if you notice me of any errors.

I'm not really programmer nor web developer, however I like programming PHP and JavaScript. In fact I'm an [architekt](http://marekmurawski.pl).