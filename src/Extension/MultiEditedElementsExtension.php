<?php

namespace Symbiote\Elemental\Extension;

use Extension;
use FieldList;
use BaseElement;
use Permission;
use ElementImage;
use TextareaField;
use CheckboxField;
use ElementList;
use UploadField;
use MultiRecordUploadField;
use MultiRecordField;
use ClassInfo;


/**
 * @author marcus
 */
class MultiEditedElementsExtension extends Extension
{

    public function updateCMSFields(\FieldList $fields)
    {
        if (!BaseElement::config()->enable_title_in_template) {
            $fields->removeByName('HideTitle');
        }

        if (!Permission::check('ADMIN')) {
            $fields->removeByName('Type');
            $fields->removeByName('MoveToListID');
        }

        if ($this->owner instanceof ElementImage && $this->owner->class == 'ElementImage')
        {
            // don't need this
            $fields->removeByName('LinkDescription');

            // replace the caption with a text field instead of html
            // if they're not a power user, only add caption if there's content already defined; ie we don't want
            // an empty field showing later
            $fields->replaceField('Caption', TextareaField::create('Caption', 'Image related text'));
        }

        $fields->removeByName('HideLink');
        if ($fields->dataFieldByName('LinkText'))
        {
            $fields->insertAfter('LinkText', CheckboxField::create('HideLink', 'Hide Link?'));
        }
    }

    public function updateMultiEditFields(FieldList $fields)
    {
        $fields->removeByName('VirtualClones');
        // @TODO disable for the moment
        if (!Permission::check('ADMIN'))
        {
            $fields->removeByName('RenderWithTemplate');
            $fields->removeByName('ExtraClass');

            // let's look for a list gridfield
            $editorFields = $this->owner->config()->element_editor_fields;

            if ($this->owner instanceof ElementList)
            {
                // remove the gridfield
                $fields->removeByName('Elements');
            }

            $dataFields = $fields->dataFields();
            if ($editorFields)
            {
                $editorFields = array_unique($editorFields);
                $keepFields = array();

                foreach ($editorFields as $keepIt)
                {
                    $f = $fields->dataFieldByName($keepIt);
                    if ($f)
                    {
                        $keepFields[$keepIt] = $f;
                    }
                    if ($keepIt == 'Title')
                    {
                        $f->setRightTitle('');
                    }
                }

//                foreach ($dataFields as $f) {
//                    $keepFields[$f->getName()] = $f;
//                }

                while ($fields->count())
                {
                    $fields->shift();
                }
                // re-add the ones we want in the order we need them
                foreach ($editorFields as $keepIt)
                {
                    if (isset($keepFields[$keepIt]))
                    {
                        $fields->push($keepFields[$keepIt]);
                    }
                }
            }
        }


        // check all our fields for file uploads and replace them
        if (class_exists('MultiRecordUploadField')) {
            $df = $fields->dataFields();
            foreach ($df as $f) {
                if ($f instanceof UploadField) {
                    $uploadField = MultiRecordUploadField::create($f->getName(), $f->Title(), $f->getItems());

                    $uploadField->setAllowedExtensions($f->getAllowedExtensions())
                        ->setTemplateFileButtons($f->getTemplateFileButtons())
                        ->setFolderName($f->getFolderName())
                        ->setCanPreviewFolder($f->canPreviewFolder())
                        ->setDisplayFolderName($f->getDisplayFolderName());

//                    $fields->replaceField($f->getName(), $uploadField);
                }
            }
        }

        if ($this->owner instanceof ElementList && class_exists('MultiRecordField'))
        {
            // replace with editor field
            $editor = MultiRecordField::create('ElementListEditor' . $this->owner->ID, 'Items', $this->owner->Elements());
            // adding elements inline doesn't quite work well enough just yet. 
//            $editor->setCanAddInline(false);

            $classes = ClassInfo::subclassesFor('BaseElement');
            $editor->setModelClasses($classes);

            $fields->push($editor);
        }
    }

}
