XcoreCMS/InlineEditing
======================

[![Build Status](https://travis-ci.org/XcoreCMS/InlineEditing.svg?branch=master)](https://travis-ci.org/XcoreCMS/InlineEditing)
[![Coverage Status](https://coveralls.io/repos/github/XcoreCMS/InlineEditing/badge.svg?branch=master)](https://coveralls.io/github/XcoreCMS/InlineEditing?branch=master)

Inline Editing = Content editable...


Requirements
------------

XcoreCMS/InlineEditing requires PHP 7.4 or higher


Installation
------------

The best way to install XcoreCMS/InlineEditing is using [Composer](http://getcomposer.org/):

```bash
    composer require xcore/inline-editing
```

#### Create database table example

```bash
    vendor/bin/inline dns="mysql:host=127.0.0.1;dbname=test" username=root password=pass tableName=table

    # parameters:
    #   dns - required
    #   username - required
    #   password - optional
    #   tableName - optional (default `inline_content`)
```
