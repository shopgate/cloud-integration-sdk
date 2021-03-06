<?php

/**
 * Copyright Shopgate GmbH.
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
 * @copyright Shopgate GmbH
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 */

namespace Shopgate\ConnectSdk\Dto\Segmentation\Segment;

use Shopgate\ConnectSdk\Dto\Base;

/**
 * @method getCode()
 */
class Create extends Base
{
    /**
     * @var array
     */
    protected $schema = [
        'type' => 'object',
        'properties' => [
            'code' => ['type' => 'string'],
            'name' => ['type' => 'string'],
            'description' => ['type' => 'string'],
            'type' => [
                'type' => 'string',
                'enum' => ['fixed', 'dynamic']
            ],
            'rules' => [
                '$ref' => Rules::class,
                'skipValidation' => true
            ],
            'status' => [
                '$ref' => Status::class,
                'skipValidation' => true
            ],
            'externalUpdateDate' => ['type' => 'string'],
        ],
        'additionalProperties' => true,
    ];
}
