---
title: OPAG
layout: home
nav_order: 2
---

# What is OPAG ?

OpenApi PHP Attributes Generator is a library made to automatically generate a valid OA3 file which describes your API.
Each path is described in your PHP files (often your controllers)
using [PHP 8 attributes](https://stitcher.io/blog/attributes-in-php-8).

## Why should I use OpenApi in the first place ?

Having a file describing your API will allow you to share it with your community (if it's public) and it can be used
with softwares such as Swagger or Postman.

## Recommendations

We recommend having some knowledge on OpenApi: https://spec.openapis.org/oas/latest.html

## Which version is compatible

Only the version 3 is compatible with this library

# How to read this documentation

- The documentation is split into 3 sections: the header, paths and components
- Mandatory and optional fields are described in the Open Api [documentation](https://spec.openapis.org/oas/latest.html)
- Optional fields can be accessed by respecting the parameters order or using the syntax `#[Element(prop1: "value1")]`
