<?php

namespace Symbiote\Elemental\Page;

use Page;

use ElementalGridFieldAddNewMultiClass;
use Symbiote\Elemental\GridField\ElementalGridFieldAddNewDefinedElement;
use SilverStripe\Forms\GridField\GridFieldDeleteAction;
use DNADesign\Elemental\Extensions\ElementalPageExtension;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use Symbiote\MultiRecord\MultiRecordEditingField;

/**
 * 
 *
 * @author marcus
 */
class ElementaryPage extends Page
{
    private static $extensions = [ElementalPageExtension::class];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->removeByName('Content');

        if ($this->ElementalAreaID) {
            $editor = MultiRecordEditingField::create('ElementEditor', 'Elements', $this->ElementalArea()->Elements(), false);
            $fields->addFieldToTab('Root.Elements', $editor);
        } 

        $grid = $fields->dataFieldByName('ElementalArea');
        $fields->addFieldToTab('Root.Elements', $grid);
        $grid->getConfig()->removeComponentsByType('ElementalGridFieldAddNewMultiClass');
        $grid->getConfig()->removeComponentsByType(GridFieldDeleteAction::class);

        return $fields;
    }

    public function onBeforeWrite()
    {
        if ($this->ElementalAreaID) {
            $content = DBField::create_field('Text', $this->getElementsForSearch());
            $this->Content = $content->RAW();
        }

        parent::onBeforeWrite();
    }

    public function getFrontendCreateFields()
    {
        $fields = FieldList::create([
            TextField::create('Title')
        ]);

        return $fields;
    }


    public function getFrontEndFields($params = null)
    {
        $fields = FieldList::create([
            TextField::create('Title')
        ]);

        if ($this->ID) {
            $elements = $this->ElementalArea()->Elements();
            if ($elements && $elements->count()) {
                $fields->push(MultiRecordEditingField::create('Elements', 'Items', $elements)->setUseToggles(false));
            }
        }

        return $fields;
    }
}