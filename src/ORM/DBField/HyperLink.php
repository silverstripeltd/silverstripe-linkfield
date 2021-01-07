<?php

declare(strict_types=1);

namespace SilverStripe\LinkField\ORM\DBField;

use SilverStripe\Core\Convert;


class HyperLink extends Link
{
    public static function getCompositeDbFields(): array
    {
        return [
            'Title' => 'Varchar',
            'URL'  => 'Varchar'
        ];
    }

    /**
     * Fallback if the template couldn't be found
     *
     * This method doesn't execute by default, because we have the template
     *
     * @see templates/SilverStripe/LinkField/ORM/DBField/HyperLink.ss
     */
    public function XML()
    {
        $url = Convert::raw2htmlatt($this->getField('URL'));
        $title = Convert::raw2xml($this->getField('Title'));

        return "<a href='$url'>$title</a>";
    }
}
