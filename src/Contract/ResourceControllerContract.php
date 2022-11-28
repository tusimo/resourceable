<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Contract;

use Tusimo\Resource\Entity\ResourceRequest;
use Tusimo\Resource\Entity\ResourceResponse;

interface ResourceControllerContract
{
    /**
     * Get Resource.
     */
    public function show(ResourceRequest $request): ResourceResponse;

    /**
     * Add Resource.
     */
    public function store(ResourceRequest $request): ResourceResponse;

    /**
     * Update Resource.
     */
    public function update(ResourceRequest $request): ResourceResponse;

    /**
     * Destroy Resource.
     */
    public function destroy(ResourceRequest $request): ResourceResponse;

    /**
     * List Resource with Paginate or Get All Resource by Filter.
     */
    public function index(ResourceRequest $request): ResourceResponse;

    /**
     * List Resource by Query.
     */
    public function get(ResourceRequest $request): ResourceResponse;

    /**
     * Get Aggregate for resource.
     */
    public function aggregate(ResourceRequest $request): ResourceResponse;
}
