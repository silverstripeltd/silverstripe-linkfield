<?php

declare(strict_types=1);

namespace SilverStripe\LinkField\Forms;

use SilverStripe\Forms\FormField;
use SilverStripe\View\Requirements;


/**
 * Field designed to edit complex data passed as a JSON string. Other FormFields can be built on top of this one.
 *
 * It will output a hidden input with serialized JSON Data.
 */
class Field extends FormField
{
    protected $schemaDataType = self::SCHEMA_DATA_TYPE_CUSTOM;

    protected $inputType = 'hidden';

    public function getAttributes()
    {
        return array_merge_recursive(
            parent::getAttributes(),
            [
                'data-schema' => json_encode($this->getSchemaData()),
                'data-state' => json_encode($this->getSchemaState()),
            ]
        );
    }

    public function getSchemaComponent()
    {
        return parent::getSchemaComponent() ?? 'LinkField';
    }

    public function Field($properties = [])
    {
        Requirements::add_i18n_javascript('silverstripe/linkfield:client/lang', false, true);
        Requirements::javascript('silverstripe/linkfield:client/dist/js/bundle.js');
        Requirements::css('silverstripe/linkfield:client/dist/styles/bundle.css');

        return parent::{__FUNCTION__}($properties);
    }
}
