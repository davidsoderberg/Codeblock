<?php namespace App\Models;

/**
 * Class NotificationType
 * @package App\Models
 */
class NotificationType
{

    /**
     * Constant for string Mention.
     */
    const MENTION = 'Mention';

    /**
     * Constant for string Reply.
     */
    const REPLY = 'Reply';

    /**
     * Constant for string Comment.
     */
    const COMMENT = 'Comment';

    /**
     * Constant for string Star.
     */
    const STAR = 'Star';

    /**
     * Constant for string Role.
     */
    const ROLE = 'Role';

    /**
     * Constant for string Banned.
     */
    const BANNED = 'Banned';

    /**
     * Constant for string Favourite.
     */
    const FAVOURITE = 'Favourite';

    /**
     * Check if type exists.
     *
     * @param $type
     *
     * @return bool
     */
    public static function isType($type)
    {
        $type = strtoupper($type);
        $rc = new \ReflectionClass('App\Models\NotificationType');
        return array_key_exists($type, $rc->getConstants());
    }
}
