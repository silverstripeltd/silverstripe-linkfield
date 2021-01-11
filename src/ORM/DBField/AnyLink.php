<?php

declare(strict_types=1);

namespace SilverStripe\LinkField\ORM\DBField;

use SilverStripe\Core\ClassInfo;


/**
 * AnyLink is type that may contain any other link type internally
 * and be casted to it via `castToExactLinkType` method.
 */
class AnyLink extends Link
{
    public static function getCompositeDbFields(): array
    {
        $linkTypes = ClassInfo::getValidSubClasses(Link::class);

        $fields = [
            static::genClassFieldPrefix(__CLASS__) . 'LinkType' => 'DBClassName(\''.addcslashes(Link::class, '\'').'\')'
        ];

        foreach ($linkTypes as $linkType) {
            if (is_a($linkType, __CLASS__, true)) {
                // skipping self (AnyLink) and all children
                continue;
            }

            $ref = new \ReflectionClass($linkType);

            if ($ref->isAbstract()) {
                continue;
            }

            $fieldPrefix = static::genClassFieldPrefix($linkType);

            $dbFields = \call_user_func([$linkType, __FUNCTION__]);

            foreach ($dbFields as $fieldKey => $fieldSpec) {
                $fields[$fieldPrefix . $fieldKey] = $fieldSpec;
            }
        }

        return $fields;
    }

    private static function genClassFieldPrefix($fqcn): string
    {
        $name = explode('\\', $fqcn);
        $hash = hash('crc32b', $fqcn);

        return $name[count($name)-1] . '_' . $hash . '_';
    }

    /**
     * Cast AnyLink to the exact link type it contains (e.g. HyperLink or SiteTreeLink)
     * The DBField returned is bound to the same DataObject of this AnyLink and can
     * transparently update its fields on its own (the name of the field is set correctly)
     */
    public function castToExactLinkType(): Link
    {
        $linkType = $this->getField(static::genClassFieldPrefix(__CLASS__) . 'LinkType');

        $linkObject = call_user_func(
            [$linkType, 'create'],
            $this->getName() . static::genClassFieldPrefix($linkType)
        );

        $linkObject->bindTo($this->record);

        return $linkObject;
    }

    public function forTemplate()
    {
        return $this->castToExactLinkType()->forTemplate();
    }
}