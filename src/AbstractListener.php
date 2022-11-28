<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource;

use Tusimo\Resource\Model\Events\Saved;
use Tusimo\Resource\Model\Events\Saving;
use Tusimo\Resource\Model\Events\Created;
use Tusimo\Resource\Model\Events\Deleted;
use Tusimo\Resource\Model\Events\Updated;
use Tusimo\Resource\Model\Events\Creating;
use Tusimo\Resource\Model\Events\Deleting;
use Tusimo\Resource\Model\Events\Restored;
use Tusimo\Resource\Model\Events\Updating;
use Tusimo\Resource\Model\Events\Restoring;
use Tusimo\Resource\Model\Events\Retrieved;
use Tusimo\Resource\Model\Events\ForceDeleted;

/**
 * @method retrieved(Retrieved $event)
 * @method creating(Creating $event)
 * @method created(Created $event)
 * @method updating(Updating $event)
 * @method updated(Updated $event)
 * @method saving(Saving $event)
 * @method saved(Saved $event)
 * @method restoring(Restoring $event)
 * @method restored(Restored $event)
 * @method deleting(Deleting $event)
 * @method deleted(Deleted $event)
 * @method forceDeleted(ForceDeleted $event)
 */
abstract class AbstractListener
{
}
