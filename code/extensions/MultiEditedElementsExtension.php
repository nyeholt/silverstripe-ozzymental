<?php

/**
 * @author marcus
 */
class MultiEditedElementsExtension extends Extension
{

    public function updateCMSFields(\FieldList $fields)
    {
        $fields->removeByName('HideTitle');

        $fields->removeByName('Type');
        $fields->removeByName('MoveToListID');


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

        if ($this->owner instanceof ElementList && class_exists('MultiRecordField'))
        {
            $fields->removeByName('ListDescription');
            // replace with editor field
            $fields->push(MultiRecordField::create('ElementListEditor' . $this->owner->ID, 'Items', $this->owner->Elements()));
        }
    }

}
