<?php

namespace Symbiote\Elemental\Page;

use Page;

use ElementalGridFieldAddNewMultiClass;
use Symbiote\MultiRecordField\Field\MultiRecordField;
use Symbiote\Elemental\GridField\ElementalGridFieldAddNewDefinedElement;
use SilverStripe\Forms\GridField\GridFieldDeleteAction;
use DNADesign\Elemental\Extensions\ElementalPageExtension;

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

        if (class_exists(MultiRecordField::class) && $this->ElementAreaID) {
            $editor = MultiRecordField::create('ElementEditor', 'Elements', $this->ElementArea()->Elements());
            $editor->setCanAddInline(false);
            $fields->addFieldToTab('Root.Main', $editor);
        }

        $grid = $fields->dataFieldByName('ElementalArea');
        $fields->addFieldToTab('Root.ManageElements', $grid);

        $grid->getConfig()->removeComponentsByType('ElementalGridFieldAddNewMultiClass');
        // $grid->getConfig()->addComponent(new ElementalGridFieldAddNewDefinedElement());
        // $grid->getConfig()->addComponent(new ElementalGridFieldAddNewMultiClass());
        $grid->getConfig()->removeComponentsByType(GridFieldDeleteAction::class);

        return $fields;
    }
}