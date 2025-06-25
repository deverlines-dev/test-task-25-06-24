<?php

namespace App\Models;

use App\Models\Traits\RowIdTrait;
use Illuminate\Auth\Authenticatable as AuthenticatableTrait;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\Access\Authorizable as AuthorizableTrait;

/**
 * @property string $name
 * @property string $password
 *
 * @property Collection<UserScore> $scores
 * @property Collection<UserBookmark> $bookmarks
 * @property Collection<UserProperty> $properties
 */
class User extends AbstractModel implements AuthenticatableContract, AuthorizableContract
{
    use AuthenticatableTrait, AuthorizableTrait, RowIdTrait;

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'row_id' => 'string',
            'name' => 'string',
            'password' => 'hashed',
        ];
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return HasMany<UserScore>
     */
    public function scores(): HasMany
    {
        return $this->hasMany(UserScore::class, 'user_id');
    }

    /**
     * @return HasMany<UserBookmark>
     */
    public function bookmarks(): HasMany
    {
        return $this->hasMany(UserBookmark::class, 'user_id');
    }

    /**
     * @return HasMany<UserProperty>
     */
    public function properties(): HasMany
    {
        return $this->hasMany(UserProperty::class, 'user_id');
    }
}
