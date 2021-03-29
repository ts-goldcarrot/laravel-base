<?php
/**
 * Created date 04.08.2020
 * @author Sergey Tyrgola <ts@GoldcarrotLaravel\Base.ru>
 */

namespace GoldcarrotLaravel\Base\Enums;


class BaseStatusEnums extends BaseEnums
{
    public const ACTIVE = 'active';
    public const INACTIVE = 'inactive';
    public const BANNED = 'banned';
    public const DELETED = 'deleted';

    public static function keys(): array
    {
        return [
            self::ACTIVE,
            self::INACTIVE,
            self::DELETED,
        ];
    }

}
