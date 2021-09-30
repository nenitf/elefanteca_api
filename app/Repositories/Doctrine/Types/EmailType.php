<?php

namespace App\Repositories\Doctrine\Types;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Core\Models\Email;

// https://www.doctrine-project.org/projects/doctrine-orm/en/2.9/cookbook/custom-mapping-types.html
class EmailType extends Type
{
    const MYTYPE = 'email'; // modify to match your type name

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        // return the SQL used to create your column type. To create a portable column type, use the $platform.
        return "VARCHAR(45)";
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        // This is executed when the value is read from the database. Make your conversions here, optionally using the $platform.
        return new Email($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        // This is executed when the value is written to the database. Make your conversions here, optionally using the $platform.
        if (!$value instanceof Email) {
            throw new \DomainException('Invalid type');
        }
        return $value->getEmail();
    }

    public function getName()
    {
        return self::MYTYPE; // modify to match your constant name
    }
}

