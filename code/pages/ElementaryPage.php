<?php

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

        $grid = $fields->dataFieldByName('ElementArea');
        $fields->addFieldToTab('Root.ManageElements', $grid);

        $grid->getConfig()->removeComponentsByType('ElementalGridFieldAddNewMultiClass');
        $grid->getConfig()->addComponent(new ElementalGridFieldAddNewDefinedElement());
        $grid->getConfig()->addComponent(new ElementalGridFieldAddNewMultiClass());
        $grid->getConfig()->removeComponentsByType('GridFieldDeleteAction');

        return $fields;
    }
}