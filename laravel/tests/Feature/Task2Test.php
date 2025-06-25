<?php


namespace Tests\Feature;

use Tests\TestCase;

/**
 * 2. В БД есть три таблицы:
 * books
 * users
 * bookmarks
 *
 * Нужно возвращать данные по всем закладкам пользователя, включая информацию о названии и описании книги,
 * в формате json-ответа для REST API.
 *
 * Примерное описание жизненного цикла запроса:
 * route > middleware > requestDto > validation > controller > service > responseDto > jsonResponse
 *
 */
class Task2Test extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function test_task(): void
    {
        $response = $this->getJson('api/user/user-bookmarks/list', ['token' => 'Bearer: 1234qwer1234qwer']);

        $response->assertJsonStructure([ // проверка структуры ответа
            'items' => [
                '*' => [
                    'id',
                    'bookmark',
                    'book' => [
                        'id',
                        'title',
                        'description',
                    ],
                ],
            ],
            'pagination' => []
        ]);
    }
}
