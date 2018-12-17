<?php

namespace Symbiote\Elemental\Page;

use Page;

use ElementalGridFieldAddNewMultiClass;
use Symbiote\MultiRecordField\Field\MultiRecordField;
use Symbiote\Elemental\GridField\ElementalGridFieldAddNewDefinedElement;
use SilverStripe\Forms\GridField\GridFieldDeleteAction;
use DNADesign\Elemental\Extensions\ElementalPageExtension;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;

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

        if (class_exists(MultiRecordField::class) && $this->ElementAreaID) {
            $editor = MultiRecordField::create('ElementEditor', 'Elements', $this->ElementArea()->Elements());
            $editor->setCanAddInline(false);
            $fields->addFieldToTab('Root.Main', $editor);
        }

        $grid = $fields->dataFieldByName('ElementalArea');

        $grid->getConfig()->removeComponentsByType('ElementalGridFieldAddNewMultiClass');
        // $grid->getConfig()->addComponent(new ElementalGridFieldAddNewDefinedElement());
        // $grid->getConfig()->addComponent(new ElementalGridFieldAddNewMultiClass());
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
                $fields->push(MultiRecordField::create('Elements', 'Items', $elements)->setUseToggles(false));
            }
        }

        return $fields;
    }
}