<?php

declare(strict_types=1);

namespace SilverStripe\LinkField\ORM\DBField;

use SilverStripe\ORM\FieldType\DBComposite;
use SilverStripe\View\SSViewer;
use SilverStripe\LinkField\Forms;


abstract class Link extends DBComposite
{
    abstract public static function getCompositeDbFields(): array;

    /**
     * Prevent composite_db from getting overriden
     * in children as private statics (which is a shot in the foot).
     * 
     * Enforces use of the `getCompositeDbFields` method instead.
     * That ensures the DBLink_Type field has the correct definition
     * for BASE link type and its inheritance chain only.
     * Otherwise, the whole DBLink inheritance chain would be accepted.
     */
    protected $composite_db = [];

    /**
     * Defines the BASE link type that should be inherited by all
     * implementations that can be persisted 
     * 
     * Example:
     * <?php
     *   class Page {
     *       private static $db = [
     *           'link' => HypertextLink::class
     *       ];
     *   }
     *
     *   Page.link = new DropboxDocumentLink();
     * ?>
     * 
     * In the example above
     *    - HypertextLink is the BASE link type
     *    - DropboxDocumentLink is the IMPLEMENTATION (a child of HypertextLink)
     *    - class DropboxDocumentLink extends HypertextLink { ... }
     *    - IMPLEMENTATION cannot change database structure (add new fields)
     */
    public static function getBaseLinkType(): string
    {
        return static::class;
    }

    /**
     * Returns the DBLink_Type of the curret instance (link IMPLEMENTATION).
     */
    public function getCurrentLinkType(): string
    {
        // DBLink_Type is the class name of DBLink implementor
        return static::class;
    }

    public function getField($field)
    {
        if ($field === 'DBLink_Type') {
            return $this->getCurrentLinkType();
        }

        return parent::{__FUNCTION__}($field);
    }

    public function compositeDatabaseFields(): array
    {
        $baseLinkType = static::getBaseLinkType();

        // Always generate the field structure of the BASE link type, not IMPLEMENTATION.
        // The IMPLEMENTATION should never mutate the database structure (the list of fields).
        $compositeDbFields = call_user_func([$baseLinkType, 'getCompositeDbFields']);

        return array_merge(
            [
                'DBLink_Type' => 'DBClassName(\''.addcslashes($baseLinkType, '\'').'\')',
            ],
            $compositeDbFields
        );
    }

    public function scaffoldFormField($title = null, $params = null)
    {
        return Forms\Field::create($this->getName(), $title);
    }

    public function forTemplate()
    {
        $templates = SSViewer::get_templates_by_class($this->getCurrentLinkType());
        $template = SSViewer::chooseTemplate($templates);

        if (!$template) {
            return parent::{__FUNCTION__}();
        }

        return $this->renderWith($template);
    }
}
