<?php

namespace Symbiote\Elemental\GridField;

use GridFieldAddNewMultiClass;
use GridField;
use Config;
use GridFieldExtensions;
use DropdownField;
use ArrayData;
use Controller;
use Exception;
use SS_HTTPResponse_Exception;
use BaseElement;
use Object;



class ElementalGridFieldAddNewDefinedElement extends GridFieldAddNewMultiClass {
	private static $templated_elements = array();
    
	public function getClasses(\GridField $grid) {
		$classes = array();
		$items = Config::inst()->get('ElementalGridFieldAddNewDefinedElement', 'templated_elements');
		if ($items && count($items)) {
			foreach ($items as $name => $details) {
				$label = ucwords(trim(strtolower(preg_replace('/_?([A-Z])/', ' $1', $name))));
				$classes[$name] = $label;
			}
		}
		return $classes;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getHTMLFragments($grid) {
		
		$classes = $this->getClasses($grid);
		if(!count($classes)) {
			return array();
		}
		GridFieldExtensions::include_requirements();
		$field = new DropdownField(sprintf('%s[ClassName]', __CLASS__), '', $classes);
		if (Config::inst()->get('GridFieldAddNewMultiClass', 'showEmptyString')) {
			$field->setEmptyString('(Element set)');
		}
		$field->addExtraClass('no-change-track');
		$data = new ArrayData(array(
			'Title'      => $this->getTitle(),
			'Link'       => Controller::join_links($grid->Link(), 'add-combined-class', '{class}'),
			'ClassField' => $field
		));
		return array(
			$this->getFragment() => $data->renderWith('GridFieldAddNewMultiClass')
		);
	}
	public function handleAdd($grid, $request) {
		$class     = $request->param('ClassName');
		$classes   = $this->getClasses($grid);
		$component = $grid->getConfig()->getComponentByType('GridFieldDetailForm');
		if(!$component) {
			throw new Exception('The add new multi class component requires the detail form component.');
		}
		if(!$class || !array_key_exists($class, $classes)) {
			throw new SS_HTTPResponse_Exception(400);
		}
		$element = null;
		$items = Config::inst()->get('ElementalGridFieldAddNewDefinedElement', 'templated_elements');
		if ($items && count($items) && isset($items[$class])) {
			$config = $items[$class];
            $list = $grid->getList();

            $dummy = BaseElement::create();
            if ($dummy->hasMethod('createTemplatedElements')) {
                $elementsToCreate = isset($config['elements']) ? $config['elements'] : [$config];
                $dummy->createTemplatedElements($elementsToCreate, $list);
            }
		} else {
            
		}

		if ($element && !$element->ID) {
			// go through the process
			$handler = Object::create($this->itemRequestClass,
				$grid, $component, $element, $grid->getForm()->getController(), 'add-combined-class'
			);
			$handler->setTemplate($component->getTemplate());
			return $handler;
		} else {
			// redirect back to where we came from as the element will be populated-ish
			return Controller::curr()->redirectBack();
		}
	}

    protected function createElement($config) {
        if (!isset($config['ClassName'])) {
            throw new Exception('ClassName not found for new defined element');
        }
        
        $elClass = $config['ClassName'];
        $element = $elClass::create();
        $element->update($config);
        $element->write();

        return $element;
    }
        

	/**
	 * {@inheritDoc}
	 */
	public function getURLHandlers($grid) {
		return array(
			'add-combined-class/$ClassName!' => 'handleAdd'
		);
	}
}