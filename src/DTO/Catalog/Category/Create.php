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

namespace Shopgate\ConnectSdk\Dto\Catalog\Category;

use Shopgate\ConnectSdk\Dto\Catalog\Category;
use Shopgate\ConnectSdk\Dto\LocalizedString;

/**
 * Default class that handles validation for category Create payloads.
 *
 * @method Create setCode(string $code)
 * @method Create setParentCategoryCode(string $parentCategoryCode)
 * @method Create setImage(string $image)
 * @method Create setName(LocalizedString $name)
 * @method Create setUrl(string $url)
 * @method Create setSequenceId(integer $sequenceId)
 * @method Create setDescription(LocalizedString $description)
 * @method Create setExternalUpdateDate(string $externalUpdateDate)
 */
class Create extends Category
{
    /**
     * @var array
     * @codeCoverageIgnore
     */
    protected $schema = [
        'type'                 => 'object',
        'properties'           => [
            'code'               => ['type' => 'string'],
            'parentCategoryCode' => ['type' => 'string'],
            'image'              => ['type' => 'string'],
            'name'               => ['type' => 'object'],
            'url'                => ['type' => 'string'],
            'sequenceId'         => ['type' => 'integer'],
            'description'        => ['type' => 'object'],
            'externalUpdateDate' => ['type' => 'string'],
        ],
        'additionalProperties' => true
    ];
}
