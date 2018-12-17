# OzzyMental - some Elemental extensions

Adds in

* ElementaryPage - a page type that uses elements
* EmbeddedItemElement - An element type that uses OEmbed for embedding arbitrary URL content
* TemplatedElementExtension - with the User Templates module, allows for elements to be individually
  templated. 

## Composer Install

```
composer require nyeholt/silverstripe-ozzymental:~2.0
```

## Requirements

* SilverStripe 4.1+

## Configuration

ElementaryPage can be used out of the box as a new page type when creating a page

EmbeddedItemElement will be available as an element to add

TemplatedElementExtension - NOT AVAILABLE IN SS4 as yet

## Templated elements

Add in User Templates module, and you can choose a CMS managed template to be bound to the template

You'll need to add an ElementHolder.ss template to your theme that contains something like

```

$TemplatedContent

```

to make use of it. 






