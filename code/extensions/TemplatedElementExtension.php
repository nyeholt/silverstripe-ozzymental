<?php

/**
 * @author Stephen McMahon <stephen@silverstripe.com.au>
 */
class TemplatedElementExtension extends DataExtension {

	private static $db = array(
		'RenderWithTemplate' => 'Varchar(128)', // allows developers access to specify a different template at create
	);

    /** 
     * This is actually defined in .yml so that it can be conditionally bound _ONLY_ if the 
     * UserTemplates module exists
     */
//	public static $has_one = array(
//		'LayoutTemplate' => 'UserTemplate',
//	);
	
	public function onBeforeWrite() {
		if(intval($this->owner->RenderWithTemplate) > 0) {
			$this->owner->LayoutTemplateID = $this->owner->RenderWithTemplate;
		} else {
			$this->owner->LayoutTemplateID = 0;
		}
	}
	
	
	public function updateCMSFields(\FieldList $fields) {

		$fields->removeByName('RenderWithTemplate');
		
		if (Permission::check('ADMIN')) {
			// get the list of templates
			$fields->replaceField('LayoutTemplateID',
				DropdownField::create(
					'RenderWithTemplate',
					'Display template',
					$this->getElementTemplateList()
				)->setEmptyString('-- default --')
			);
		}
	}

	public function getElementTemplateList() {
		$layouts = class_exists('UserTemplate') ? DataList::create('UserTemplate')->filter(array('Use' => 'Layout')) : null;
        if (!$layouts) {
            return [];
        }

		$themeDir = Config::inst()->get('SSViewer', 'theme');
		$templates = array();
		if (strlen($themeDir)) {
			$path = Director::baseFolder() . '/themes/' . $themeDir . '/templates/elements/*.ss';
			$files = glob($path);
			foreach ($files as $filename) {
				$templateName = str_replace('.ss', '', basename($filename));
				$templates[$templateName] = $templateName;
			}
		}

		foreach($layouts->map() as $ID => $title) {
			$templates[$ID] = $title;
		}
		
		return $templates;
	}
}
