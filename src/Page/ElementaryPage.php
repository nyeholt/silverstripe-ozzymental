<?php

namespace Symbiote\Elemental\Page;

use Page;
use MultiRecordField;
use ElementalGridFieldAddNewDefinedElement;
use ElementalGridFieldAddNewMultiClass;


/**
 * 
 *
 * @author marcus
 */
class ElementaryPage extends Page
{
    private static $extensions = ['ElementPageExtension'];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        if (class_exists('MultiRecordField') && $this->ElementAreaID) {
            $editor = MultiRecordField::create('ElementEditor', 'Elements', $this->ElementArea()->Elements());
            $editor->setCanAddInline(false);
            $fields->addFieldToTab('Root.Main', $editor);
        }

        $grid = $fields->dataFieldByName('ElementArea');
        $fields->addFieldToTab('Root.ManageElements', $grid);

        $grid->getConfig()->removeComponentsByType('ElementalGridFieldAddNewMultiClass');
        $grid->getConfig()->addComponent(new ElementalGridFieldAddNewDefinedElement());
        $grid->getConfig()->addComponent(new ElementalGridFieldAddNewMultiClass());
        $grid->getConfig()->removeComponentsByType('GridFieldDeleteAction');

        return $fields;
    }
}