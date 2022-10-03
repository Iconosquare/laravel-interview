<?php

namespace App\Models;

class PostStatus
{

    public const DRAFT = 'draft';
    public const PUBLISHED = 'published';

    /**
     * Get all status types
     *
     * @return string[]
     */
    public static function all()
    {

        return [
            self::PUBLISHED,
            self::DRAFT
        ];
    }
}
