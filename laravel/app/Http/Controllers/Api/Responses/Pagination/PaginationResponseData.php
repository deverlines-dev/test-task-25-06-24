<?php

namespace App\Http\Controllers\Api\Responses\Pagination;

use Illuminate\Pagination\LengthAwarePaginator;

class PaginationResponseData
{
    public function __construct(
        public int $currentPage,
        public ?int $from,
        public int $lastPage,
        public string $path,
        public int $perPage,
        public ?int $to,
        public int $total,
        /** @var PaginationResponseData */
        public array $links = [],
    ) {
    }

    public static function fromPaginator(LengthAwarePaginator $paginator): static
    {
        /**
         * @todo тут лучше по-адекватнее сделать
         */
        $data = $paginator->toArray();

        return new static(
            currentPage: $data['current_page'],
            from: $data['from'],
            lastPage: $data['last_page'],
            path: $data['path'],
            perPage: $data['per_page'],
            to: $data['to'],
            total: $data['total'],
            links: $data['links'],
        );

    }
}
