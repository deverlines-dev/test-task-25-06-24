<?php

namespace App\Http\Controllers\Api\User\UserBookmarks;

use App\Http\Controllers\AbstractApiController;
use App\Http\Controllers\Api\Book\Responses\BookPreviewItemData;
use App\Http\Controllers\Api\Responses\Pagination\PaginationResponseData;
use App\Http\Controllers\Api\User\UserBookmarks\Responses\UserBookmarksListItemData;
use App\Http\Controllers\Api\User\UserBookmarks\Responses\UserBookmarksListJsonResponse;
use App\Http\Repositories\UserBookmarksRepository;
use App\Models\UserBookmark;
use Illuminate\Http\JsonResponse;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Group;

#[Group(prefix: 'user/user-bookmarks', as: 'user.user_bookmarks.')]
readonly class UserBookmarksApiController extends AbstractApiController
{
    public function __construct(private UserBookmarksRepository $userBookmarksRepository)
    {

    }

    /**
     * @todo добавить swagger
     */
    #[Get(uri: 'list', middleware: ['api.auth'])]
    public function list(): JsonResponse
    {
        $userBookmarks = $this->userBookmarksRepository
            ->query()
            ->where('user_id', $this->getAuthUser()->getId())
            ->with('book')
            ->paginate();

        $response = new UserBookmarksListJsonResponse();
        $response->setPagination(PaginationResponseData::fromPaginator($userBookmarks));

        $userBookmarks->each(function (UserBookmark $userBookmark) use ($response) {
            $response->addItem(new UserBookmarksListItemData(
                id: $userBookmark->getId(),
                bookmark: $userBookmark->getBookmark(),
                book: new BookPreviewItemData(
                    id: $userBookmark->book->getId(),
                    title: $userBookmark->book->getTitle(),
                    description: $userBookmark->book->getDescription(),
                )
            ));
        });

        return new JsonResponse($response);
    }
}
