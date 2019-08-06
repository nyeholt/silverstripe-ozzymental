<?php

namespace Symbiote\Elemental\Element;


use Sheadawson\Linkable\Models\EmbeddedObject;
use Sheadawson\Linkable\Forms\EmbeddedObjectField;
use DNADesign\Elemental\Models\BaseElement;


/**
 * Allows the embedding of a youtube video
 *
 * @author marcus
 */
class EmbeddedItemElement extends BaseElement
{
    private static $title = 'Embedded item';

    private static $table_name = 'EmbeddedItemElement';

    private static $description = 'An embeddable item, such as video, image, or other website';

    private static $has_one = array(
        'EmbeddedItem'      => EmbeddedObject::class,
    );


    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->removeByName('EmbeddedItemID');
        $i = $this->EmbeddedItem();
        $fields->addFieldToTab('Root.Main', EmbeddedObjectField::create('EmbeddedItem', 'Item URL', $this->EmbeddedItem()));

        return $fields;
    }

    public function onBeforeWrite()
    {
        parent::onBeforeWrite();
        $item = $this->EmbeddedItem();
        if ($item && $item->ID && strlen($item->Title)) {
            $this->Title = $item->Title;
        }
    }

    public function getType()
    {
        return _t(__CLASS__ . '.BlockType', 'Embedded Item');
    }
}
