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

namespace Shopgate\ConnectSdk\Dto\Catalog\Product\Dto;

use Shopgate\ConnectSdk\Dto\Base as DtoBase;

/**
 * @method ShippingInformation setIsShippedAlone(boolean $isShippedAlone)
 * @method ShippingInformation setHeight(float $height)
 * @method ShippingInformation setHeightUnit(string $heightUnit)
 * @method ShippingInformation setWidth(float $width)
 * @method ShippingInformation setWidthUnit(string $widthUnit)
 * @method ShippingInformation setLength(float $length)
 * @method ShippingInformation setLengthUnit(string $lengthUnit)
 * @method ShippingInformation setWeight(float $weight)
 * @method ShippingInformation setWeightUnit(string $weightUnit)
 *
 * @method boolean getIsShippedAlone()
 * @method float getHeight()
 * @method string getHeightUnit()
 * @method float getWidth()
 * @method string getWidthUnit()
 * @method float getLength()
 * @method string getLengthUnit()
 * @method float getWeight()
 * @method string getWeightUnit()
 */
class ShippingInformation extends DtoBase
{
    /**
     * @var array
     * @codeCoverageIgnore
     */
    protected $schema = [
        'type'                 => 'object',
        'properties'           => [
            'isShippedAlone' => ['type' => 'boolean'],
            'height'         => ['type' => 'number'],
            'heightUnit'     => ['type' => 'string'],
            'width'          => ['type' => 'number'],
            'widthUnit'      => ['type' => 'string'],
            'length'         => ['type' => 'number'],
            'lengthUnit'     => ['type' => 'string'],
            'weight'         => ['type' => 'number'],
            'weightUnit'     => ['type' => 'string']
        ],
        'additionalProperties' => true
    ];
}
