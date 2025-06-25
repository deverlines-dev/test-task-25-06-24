<?php

namespace App\Http\Controllers\Api\Book\Responses;

readonly class BookPreviewItemData
{
    public function __construct(public int $id, public string $title, public string $description)
    {

    }
}
