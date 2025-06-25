<?php

declare(strict_types=1);

namespace App\Models\Traits;

use App\Models\AbstractModel;
use Illuminate\Support\Str;

/**
 * @property string $rowid
 *
 * @mixin AbstractModel
 */
trait RowIdTrait
{
    public function getRowId(): string
    {
        return $this->rowid;
    }

    public function setRowId(string $rowid): static
    {
        $this->rowid = $rowid;

        return $this;
    }

    public function makeRowId(): static
    {
        $this->rowid = Str::uuid()->getBytes();

        return $this;
    }
}
