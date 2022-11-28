<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Contract;

interface ResourceCleanAble
{
    /**
     * clean resource.
     */
    public function shouldClean(): bool;

    /**
     * Delete cache by resource id.
     *
     * @param int|string $id
     */
    public function deleteResourceCache($id);

    /**
     * Delete cache by resource ids.
     */
    public function deleteResourcesCache(array $ids);
}
