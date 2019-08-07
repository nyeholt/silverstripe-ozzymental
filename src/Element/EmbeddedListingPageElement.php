<?php

namespace Symbiote\Elemental\Element;

use DNADesign\Elemental\Models\BaseElement;
use Symbiote\ListingPage\ListingPage;
use SilverStripe\ORM\FieldType\DBField;

if (!class_exists(ListingPage::class)) {
    return;
}

class EmbeddedListingPageElement extends BaseElement
{
    private static $table_name = 'EmbeddedListingPageElement';

    private static $has_one = [
        'ListingPage' => ListingPage::class
    ];

    public function getType()
    {
        return _t(__CLASS__ . '.BlockType', 'Content listing');
    }

    public function getListingContent()
    {
        $page = $this->ListingPage();
        if (!$page || !($page instanceof ListingPage)) {
            return;
        }

        $oldContent = $page->Content;
        $page->Content = '$Listing';
        $content = DBField::create_field('HTMLText', $page->Content());
        $page->Content = $oldContent;

        return $content;
    }
}
