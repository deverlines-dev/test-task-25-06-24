<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\RowIdTrait;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $property_key
 * @property string $property_value
 *
 * @property User $user
 */
class UserProperty extends AbstractModel
{
    use RowIdTrait;

    protected function casts(): array
    {
        return [
            'row_id' => 'string',
            'property_key' => 'string',
            'property_value' => 'string',
        ];
    }

    public function getPropertyKey(): string
    {
        return $this->property_key;
    }

    public function setPropertyKey(string $property_key): static
    {
        $this->property_key = $property_key;

        return $this;
    }

    public function getPropertyValue(): string
    {
        return $this->property_value;
    }

    public function setPropertyValue(string $property_value): static
    {
        $this->property_value = $property_value;

        return $this;
    }

    /**
     * @return BelongsTo<User>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
