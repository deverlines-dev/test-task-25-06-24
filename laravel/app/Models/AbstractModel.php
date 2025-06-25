<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read int $id
 *
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 */
class AbstractModel extends Model
{
    public function getId(): ?int
    {
        return $this->id;
    }
}
