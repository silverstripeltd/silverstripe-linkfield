<?php declare(strict_types=1);

namespace SilverStripe\LinkField\Tests\Models\LinkTest;

use SilverStripe\Core\Validation\ValidationResult;
use SilverStripe\Dev\TestOnly;
use SilverStripe\LinkField\Models\Link;
use SilverStripe\ORM\DataObject;

class ValidatedOwner extends DataObject implements TestOnly
{
    private static string $table_name = 'LinkField_ValidatedOwner';

    private static array $db = [
        'Title' => 'Varchar',
    ];

    private static array $has_one = [
        'Link' => Link::class,
    ];

    public function validate(): ValidationResult
    {
        $result = parent::validate();
        if (!$this->Title) {
            $result->addFieldError('Title', 'Title is required');
        }
        return $result;
    }
}
