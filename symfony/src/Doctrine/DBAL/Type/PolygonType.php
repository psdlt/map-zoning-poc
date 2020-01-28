<?php

declare(strict_types=1);

namespace App\Doctrine\DBAL\Type;

use App\Model\LineString;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class PolygonType extends Type
{
    const TYPE = 'polygon';

    /**
     * Gets the SQL declaration snippet for a field of this type.
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param array            $fieldDeclaration The field declaration.
     * @param AbstractPlatform $platform The currently used database platform.
     * @return string
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return 'POLYGON';
    }

    /**
     * Gets the name of this type.
     *
     * @return string
     */
    public function getName()
    {
        return self::TYPE;
    }

    /**
     * Modifies the SQL expression (identifier, parameter) to convert to a database value.
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param string                                    $sqlExpr
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform
     * @return string
     */
    public function convertToDatabaseValueSQL($sqlExpr, AbstractPlatform $platform)
    {
        if ($sqlExpr instanceof LineString) {
            $points = array_map(function ($point) {
                return sprintf('(%f, %f)', $point[0], $point[1]);
            }, $sqlExpr->points);
            $sqlExpr = sprintf('(%s)', implode(',', $points));
        }

        return $sqlExpr;
    }

    /**
     * Converts a value from its PHP representation to its database representation
     * of this type.
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param mixed                                     $value The value to convert.
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform The currently used database platform.
     * @return mixed The database representation of the value.
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $this->convertToDatabaseValueSQL($value, $platform);
    }

    /**
     * Modifies the SQL expression (identifier, parameter) to convert to a PHP value.
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param string           $sqlExpr
     * @param AbstractPlatform $platform
     * @return LineString
     */
    public function convertToPHPValueSQL($sqlExpr, $platform)
    {
        // ((lat, lon), (lat, lon), ...)
        $points = explode(')', trim($sqlExpr ?? '', '()'));
        $points = array_map(function ($dirty) {
            $dirty = trim($dirty, '(, ');
            $point = explode(',', $dirty);
            return [floatval($point[0]), floatval($point[1])];
        }, $points);
        return new LineString($points);
    }

    /**
     * Converts a value from its database representation to its PHP representation
     * of this type.
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param mixed                                     $value The value to convert.
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform The currently used database platform.
     * @return mixed The PHP representation of the value.
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if (!$value) {
            return $value;
        }

        return $this->convertToPHPValueSQL($value, $platform);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param AbstractPlatform $platform
     * @return bool
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }
}
