<?php

declare(strict_types=1);

namespace SilverStripe\LinkField\ORM\DBField;


class SiteTreeLink extends Link
{
    public static function getCompositeDbFields(): array
    {
        return [
            'SiteTreeId' => 'Int'
        ];
    }
}
