<?php

declare(strict_types=1);

namespace App\Models\Traits;

use App\Models\AbstractModel;

/**
 * @property string $row_id
 *
 * @mixin AbstractModel
 */
trait RowIdTrait
{
    public function getRowId(): string
    {
        return $this->row_id;
    }

    public function setRowId(string $row_id): static
    {
        $this->row_id = $row_id;

        return $this;
    }
}
