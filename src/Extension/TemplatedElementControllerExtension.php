<?php

namespace Symbiote\Elemental\Extension;

use SilverStripe\Core\ClassInfo;
use SilverStripe\Core\Extension;
use Symbiote\UserTemplates\UserTemplate;

/**
 * @author marcus
 */
class TemplatedElementControllerExtension extends Extension
{
    public function isTemplated()
    {
        $widget = $this->owner->getElement();
        return $widget && ($widget->LayoutTemplateID || strlen($widget->RenderWithTemplate));
    }

    public function TemplatedContent()
    {
        $out = '';

        $widget = $this->owner->getElement();
        if (!$widget) {
            return;
        }

        if ($widget->hasMethod('includeRequirements')) {
            $widget->includeRequirements();
        }

        $template = class_exists(UserTemplate::class) ? $widget->getComponent('LayoutTemplate') : null;

        if ($widget->LayoutTemplateID > 0 && $template) {
            $template->includeRequirements();
            $out = $this->owner->renderWith($template->getTemplateFile());
        } elseif (strlen($widget->RenderWithTemplate) && intval($widget->RenderWithTemplate) == 0) {
            $out = $this->owner->renderWith($widget->RenderWithTemplate);
        } else {
            $out = $this->owner->renderWith(array_reverse(ClassInfo::ancestry($widget->class)));
        }

        return $out;
    }

}
