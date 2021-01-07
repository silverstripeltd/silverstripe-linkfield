<?php

declare(strict_types=1);

namespace SilverStripe\LinkField\Extensions;

use SilverStripe\Core\ClassInfo;
use SilverStripe\Core\Extension;
use SilverStripe\Core\Injector\Injector;

use SilverStripe\LinkField\ORM\DBLink;


/**
 * This Extension for DataObject ensures new objects with
 * DBLink fields persist correct DBLink_Type values.
 * 
 * @see DataObject::populateDefaults method
 */
class DataObjectExtension extends Extension
{
    public function populateDefaults()
    {
        $owner = $this->getOwner();
        $schema = $owner->getSchema();
        $compositeFields = $schema->compositeFields(get_class($owner)) ?? [];

        foreach ($compositeFields as $field => $spec) {
            list($class, $args) = ClassInfo::parse_class_spec($spec);

            if (is_subclass_of($class, DBLink::class)) {
                /** @var DBLink $obj */
                $fieldObj = Injector::inst()->create($spec, $field);
                $fieldObj->setTable($schema->tableName(get_class($owner)));
                $fieldObj->saveInto($owner);
            }
        }
    }
}
