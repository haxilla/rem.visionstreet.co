<?php

namespace App\Models\Core\Concerns;

/**
 * Who a campaign row came from, using the markers the legacy data carries:
 *   admin-added (free): free = 1, admin_add = 1, campLabel = 'admin'
 *   agent's own send:   campLabel = 'area1' / 'area2', free / admin_add empty
 * Any one of the admin marks counts. Reads null-safely, so it also works when a
 * query didn't select every column.
 */
trait CampaignSource
{
    public function isAdminAdded(): bool
    {
        return (int) ($this->free ?? 0) === 1
            || (int) ($this->admin_add ?? 0) === 1
            || ($this->campLabel ?? '') === 'admin';
    }
}
