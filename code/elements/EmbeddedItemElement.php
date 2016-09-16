<?php

/**
 * Allows the embedding of a youtube video
 *
 * @author marcus
 */
class EmbeddedItemElement extends BaseElement 
{
    private static $title = 'Embedded item';
    
    private static $description = 'An embeddable item, such as video, image, or other website';
    
    private static $has_one = array(
        'EmbeddedItem'      => 'EmbeddedObject',
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
}
