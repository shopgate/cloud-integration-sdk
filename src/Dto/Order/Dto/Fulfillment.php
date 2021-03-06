<?php

/**
 * Copyright Shopgate Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @author    Shopgate Inc, 804 Congress Ave, Austin, Texas 78701 <interfaces@shopgate.com>
 * @copyright Shopgate Inc
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 */

namespace Shopgate\ConnectSdk\Dto\Order\Dto;

use Shopgate\ConnectSdk\Dto\Base;

/**
 * @method Fulfillment setId(string $id)
 * @method Fulfillment setStatus(string $status)
 * @method Fulfillment setCarrier(string $carrier)
 * @method Fulfillment setServiceLevel(string $serviceLevel)
 * @method Fulfillment setTracking(string $tracking)
 * @method Fulfillment setFulfillmentPackages(Fulfillment\FulfillmentPackage[] $fulfillmentPackages)
 * @method string getId()
 * @method string getStatus()
 * @method string getCarrier()
 * @method string getServiceLevel()
 * @method string getTracking()
 * @method Fulfillment\FulfillmentPackage[] getFulfillmentPackages()
 *
 * @codeCoverageIgnore
 */
class Fulfillment extends Base
{
    const STATUS_OPEN = 'open';
    const STATUS_CANCELED = 'canceled';
    const STATUS_IN_PROGRESS = 'inProgress';
    const STATUS_FULFILLED = 'fulfilled';

    /**
     * @var array
     */
    protected $schema = [
        'type' => 'object',
        'properties' => [
            'fulfillmentPackages' => [
                'type' => 'array',
                'items' => ['$ref' => Fulfillment\FulfillmentPackage::class, 'skipValidation' => true]
            ]
        ],
        'additionalProperties' => true,
    ];
}
