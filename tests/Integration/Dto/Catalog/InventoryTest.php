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

namespace Shopgate\ConnectSdk\Tests\Integration\Dto\Catalog;

use Shopgate\ConnectSdk\Dto\Base;
use Shopgate\ConnectSdk\Dto\Catalog\Inventory;
use Shopgate\ConnectSdk\Exception\NotFoundException;
use Shopgate\ConnectSdk\Exception\RequestException;
use Shopgate\ConnectSdk\Exception\Exception;
use Shopgate\ConnectSdk\ShopgateSdk;
use Shopgate\ConnectSdk\Tests\Integration\CatalogTest;

class InventoryTest extends CatalogTest
{
    const LOCATION_CODE = 'WHS1';

    /**
     * @throws Exception
     */
    public function createLocation()
    {
        $locations = [
            'locations' => [
                new Base([
                    'code'      => self::LOCATION_CODE,
                    'name'      => 'Test Merchant 2 Warehouse 1',
                    'status'    => 'active',
                    'latitude'  => 47.117330,
                    'longitude' => 20.681810,
                    'type'      => [
                        'code' => 'warehouse'
                    ]
                ])
            ]
        ];
        $this->sdk->getClient()->doRequest(
            [
                // general
                'requestType' => ShopgateSdk::REQUEST_TYPE_DIRECT,
                'body'        => $locations,
                'query'       => [],
                // direct
                'method'      => 'post',
                'service'     => 'omni-location',
                'path'        => 'locations',
            ]
        );
    }

    /**
     * @param $locationCode
     *
     * @throws Exception
     */
    public function deleteLocation($locationCode)
    {
        $this->sdk->getClient()->doRequest(
            [
                // general
                'requestType' => ShopgateSdk::REQUEST_TYPE_DIRECT,
                'query'       => [],
                // direct
                'method'      => 'delete',
                'service'     => 'omni-location',
                'path'        => 'locations/' . $locationCode,
            ]
        );
    }

    /**
     * @throws Exception
     */
    public function testCreateInventoryDirect()
    {
        // Arrange
        $product = $this->prepareProductMinimum();
        $this->sdk->getCatalogService()->addProducts([$product], ['requestType' => 'direct']);
        $this->createLocation();

        // Act
        $inventories = $this->provideSampleInventories(1);
        $this->sdk->getCatalogService()->addInventories($inventories, ['requestType' => 'direct']);

        // Assert
        $product          = $this->sdk->getCatalogService()->getProduct(self::PRODUCT_CODE, ['fields' => 'inventory']);
        $inventory        = $product->inventory;
        $currentInventory = new Inventory($inventory[0]);

        $this->assertEquals(self::LOCATION_CODE, $currentInventory->locationCode);
        $this->assertEquals('SKU_1', $currentInventory->sku);
        $this->assertEquals(11, $currentInventory->onHand);
        $this->assertEquals(0, $currentInventory->onReserve);
        $this->assertEquals(1, $currentInventory->safetyStock);
        $this->assertEquals(11, $currentInventory->available);
        $this->assertEquals(10, $currentInventory->visible);
        $this->assertEquals('1', $currentInventory->bin);
        $this->assertEquals('DE-1', $currentInventory->binLocation);

        // CleanUp
        $this->deleteEntitiesAfterTestRun(
            self::CATALOG_SERVICE,
            self::METHOD_DELETE_PRODUCT,
            [self::PRODUCT_CODE]
        );
        $this->deleteLocation(self::LOCATION_CODE);
    }

    /**
     * @param int $count
     *
     * @return Inventory\Create[]
     */
    private function provideSampleInventories($count = 1)
    {
        $result = [];
        for ($i = 1; $i < $count + 1; $i++) {
            $inventory = new Inventory\Create();
            $inventory->setProductCode(self::PRODUCT_CODE);
            $inventory->setLocationCode(self::LOCATION_CODE);
            $inventory->setSku('SKU_' . $i);
            $inventory->setOnHand(10 + $i);
            $inventory->setBin($i);
            $inventory->setBinLocation('DE-' . $i);
            $inventory->setSafetyStock($i);
            $result[] = $inventory;
        }

        return $result;
    }

    /**
     * @throws Exception
     */
    public function testDeleteInventoryDirect()
    {
        // Arrange
        $this->createLocation();
        $product = $this->prepareProductMinimum();
        $this->sdk->getCatalogService()->addProducts([$product], ['requestType' => 'direct']);
        $inventories = $this->provideSampleInventories(1);
        $this->sdk->getCatalogService()->addInventories($inventories, ['requestType' => 'direct']);

        // Act
        $delete = new Inventory\Delete();
        $delete->setProductCode(self::PRODUCT_CODE);
        $delete->setLocationCode(self::LOCATION_CODE);
        $delete->setSku('SKU_1');
        $this->sdk->getCatalogService()->deleteInventories([$delete], ['requestType' => 'direct']);

        // Assert
        $product = $this->sdk->getCatalogService()->getProduct(self::PRODUCT_CODE, ['fields' => 'inventory']);
        $this->assertCount(0, $product->inventory);

        // CleanUp
        $this->deleteEntitiesAfterTestRun(
            self::CATALOG_SERVICE,
            self::METHOD_DELETE_PRODUCT,
            [self::PRODUCT_CODE]
        );
        $this->deleteLocation(self::LOCATION_CODE);
    }

    /**
     * @throws Exception
     */
    public function testUpdateInventoryIncrementDirect()
    {
        // Arrange
        $this->createLocation();
        $product = $this->prepareProductMinimum();
        $this->sdk->getCatalogService()->addProducts([$product], ['requestType' => 'direct']);
        $inventories = $this->provideSampleInventories(1);
        $this->sdk->getCatalogService()->addInventories($inventories, ['requestType' => 'direct']);

        // Act
        $update = new Inventory\Update();
        $update->setProductCode(self::PRODUCT_CODE);
        $update->setLocationCode(self::LOCATION_CODE);
        $update->setSku('SKU_1');
        $update->setOperationType(Inventory\Update::OPERATION_TYPE_INCREMENT);
        $update->setOnHand(10);

        $this->sdk->getCatalogService()->updateInventories([$update], ['requestType' => 'direct']);

        // Assert
        $product = $this->sdk->getCatalogService()->getProduct(self::PRODUCT_CODE, ['fields' => 'inventory']);

        $inventory        = $product->inventory;
        $currentInventory = new Inventory($inventory[0]);

        $this->assertEquals(self::LOCATION_CODE, $currentInventory->locationCode);
        $this->assertEquals('SKU_1', $currentInventory->sku);
        $this->assertEquals(21, $currentInventory->onHand);
        $this->assertEquals(0, $currentInventory->onReserve);
        $this->assertEquals(1, $currentInventory->safetyStock);
        $this->assertEquals(21, $currentInventory->available);
        $this->assertEquals(20, $currentInventory->visible);
        $this->assertEquals('1', $currentInventory->bin);
        $this->assertEquals('DE-1', $currentInventory->binLocation);

        // CleanUp
        $this->deleteEntitiesAfterTestRun(
            self::CATALOG_SERVICE,
            self::METHOD_DELETE_PRODUCT,
            [self::PRODUCT_CODE]
        );
        $this->deleteLocation(self::LOCATION_CODE);
    }

    /**
     * @throws Exception
     */
    public function testInvalidLocationCode()
    {
        // Arrange
        $this->createLocation();
        $product = $this->prepareProductMinimum();
        $this->sdk->getCatalogService()->addProducts([$product], ['requestType' => 'direct']);
        $inventories = $this->provideSampleInventories(1);
        $inventories[0]->setLocationCode('INVALID');

        // Act
        try {
            $this->sdk->getCatalogService()->addInventories($inventories, ['requestType' => 'direct']);
        } catch (NotFoundException $exception) {
            $this->assertEquals(
                '{"code":"NotFound","message":"Locations not found: INVALID"}',
                $exception->getMessage()
            );

            return;
        } finally {
            // CleanUp
            $this->deleteEntitiesAfterTestRun(
                self::CATALOG_SERVICE,
                self::METHOD_DELETE_PRODUCT,
                [self::PRODUCT_CODE]
            );
            $this->deleteLocation(self::LOCATION_CODE);
        }

        $this->fail('Expected NotFoundException but wasn\'t thrown');
    }

    /**
     * @throws Exception
     */
    public function testInvalidProductCode()
    {
        // Arrange
        $this->createLocation();
        $product = $this->prepareProductMinimum();
        $this->sdk->getCatalogService()->addProducts([$product], ['requestType' => 'direct']);
        $inventories = $this->provideSampleInventories(1);
        $inventories[0]->setProductCode('INVALID');

        // Act
        try {
            $this->sdk->getCatalogService()->addInventories($inventories, ['requestType' => 'direct']);
        } catch (NotFoundException $exception) {
            $this->assertEquals(
                '{"code":"NotFound","message":"Products not found: INVALID"}',
                $exception->getMessage()
            );

            return;
        } finally {
            // CleanUp
            $this->deleteEntitiesAfterTestRun(
                self::CATALOG_SERVICE,
                self::METHOD_DELETE_PRODUCT,
                [self::PRODUCT_CODE]
            );
            $this->deleteLocation(self::LOCATION_CODE);
        }

        $this->fail('Expected NotFoundException but wasn\'t thrown');
    }

    /**
     * @throws Exception
     */
    public function testUpdateInventoryDecrementDirect()
    {
        // Arrange
        $this->createLocation();
        $product = $this->prepareProductMinimum();
        $this->sdk->getCatalogService()->addProducts([$product], ['requestType' => 'direct']);
        $inventories = $this->provideSampleInventories(1);
        $this->sdk->getCatalogService()->addInventories($inventories, ['requestType' => 'direct']);

        // Act
        $update = new Inventory\Update();
        $update->setProductCode(self::PRODUCT_CODE);
        $update->setLocationCode(self::LOCATION_CODE);
        $update->setSku('SKU_1');
        $update->setOperationType(Inventory\Update::OPERATION_TYPE_DECREMENT);
        $update->setOnHand(5);

        $this->sdk->getCatalogService()->updateInventories([$update], ['requestType' => 'direct']);

        // Assert
        $product = $this->sdk->getCatalogService()->getProduct(self::PRODUCT_CODE, ['fields' => 'inventory']);

        $inventory        = $product->inventory;
        $currentInventory = new Inventory($inventory[0]);

        $this->assertEquals(self::LOCATION_CODE, $currentInventory->locationCode);
        $this->assertEquals('SKU_1', $currentInventory->sku);
        $this->assertEquals(6, $currentInventory->onHand);
        $this->assertEquals(0, $currentInventory->onReserve);
        $this->assertEquals(1, $currentInventory->safetyStock);
        $this->assertEquals(6, $currentInventory->available);
        $this->assertEquals(5, $currentInventory->visible);
        $this->assertEquals('1', $currentInventory->bin);
        $this->assertEquals('DE-1', $currentInventory->binLocation);

        // CleanUp
        $this->deleteEntitiesAfterTestRun(
            self::CATALOG_SERVICE,
            self::METHOD_DELETE_PRODUCT,
            [self::PRODUCT_CODE]
        );
        $this->deleteLocation(self::LOCATION_CODE);
    }

    /**
     * @param array            $inventoryData
     * @param RequestException $expectedException
     * @param string           $missingItem
     *
     * @throws Exception
     *
     * @dataProvider provideCreateInventoryWithMissingRequiredFields
     */
    public function testCreateInventoryDirectWithMissingRequiredFields(
        array $inventoryData,
        $expectedException,
        $missingItem
    ) {
        // Arrange
        $this->createLocation();
        $product = $this->prepareProductMinimum();
        $this->sdk->getCatalogService()->addProducts([$product], ['requestType' => 'direct']);
        $inventory = new Inventory\Create($inventoryData);

        // Act
        try {
            $this->sdk->getCatalogService()->addInventories(
                [$inventory],
                [
                    'requestType' => 'direct',
                ]
            );
        } catch (RequestException $exception) {
            // Assert
            $errors  = \GuzzleHttp\json_decode($exception->getMessage(), false);
            $message = $errors->error->results->errors[0]->message;
            $this->assertInstanceOf(get_class($expectedException), $exception);
            $this->assertEquals('Missing required property: ' . $missingItem, $message);
            $this->assertEquals($expectedException->getStatusCode(), $exception->getStatusCode());

            return;
        } finally {
            // CleanUp
            $this->deleteEntitiesAfterTestRun(
                self::CATALOG_SERVICE,
                self::METHOD_DELETE_PRODUCT,
                [self::PRODUCT_CODE]
            );
            $this->deleteLocation(self::LOCATION_CODE);
        }

        $this->fail('Expected ' . get_class($expectedException) . ' but wasn\'t thrown');
    }

    /**
     * @param array $inventoryData
     * @param int   $expectedOnHand
     * @param int   $expectedSafetyStock
     * @param int   $expectedAvailable
     * @param int   $expectedVisible
     *
     * @throws Exception
     *
     * @dataProvider provideTestInventoryCalculationWithoutSafety
     */
    public function testInventoryCalculationWithoutSafety(
        $inventoryData,
        $expectedOnHand,
        $expectedSafetyStock,
        $expectedAvailable,
        $expectedVisible
    ) {
        // Arrange
        $this->createLocation();
        $product = $this->prepareProductMinimum();
        $this->sdk->getCatalogService()->addProducts([$product], ['requestType' => 'direct']);
        $inventories = $this->provideSampleInventories(1);

        $inventories[0]->setOnHand(10);
        $inventories[0]->setSafetyStock(0);

        $this->sdk->getCatalogService()->addInventories($inventories, ['requestType' => 'direct']);
        $inventory = new Inventory\Create($inventoryData);

        // Act
        $this->sdk->getCatalogService()->updateInventories([$inventory], ['requestType' => 'direct']);

        // Assert
        $product = $this->sdk->getCatalogService()->getProduct(self::PRODUCT_CODE, ['fields' => 'inventory']);

        $inventory        = $product->inventory;
        $currentInventory = new Inventory($inventory[0]);

        $this->assertEquals($expectedOnHand, $currentInventory->onHand);
        $this->assertEquals($expectedAvailable, $currentInventory->available);
        $this->assertEquals($expectedSafetyStock, $currentInventory->safetyStock);
        $this->assertEquals($expectedVisible, $currentInventory->visible);

        // CleanUp
        $this->deleteEntitiesAfterTestRun(
            self::CATALOG_SERVICE,
            self::METHOD_DELETE_PRODUCT,
            [self::PRODUCT_CODE]
        );
        $this->deleteLocation(self::LOCATION_CODE);
    }

    /**
     * @param array $inventoryData
     * @param int   $expectedOnHand
     * @param int   $expectedSafetyStock
     * @param int   $expectedAvailable
     * @param int   $expectedVisible
     *
     * @throws Exception
     *
     * @dataProvider provideTestInventoryCalculationWithSafety
     */
    public function testInventoryCalculationWithSafety(
        $inventoryData,
        $expectedOnHand,
        $expectedSafetyStock,
        $expectedAvailable,
        $expectedVisible
    ) {
        // Arrange
        $this->createLocation();
        $product = $this->prepareProductMinimum();
        $this->sdk->getCatalogService()->addProducts([$product], ['requestType' => 'direct']);
        $inventories = $this->provideSampleInventories(1);

        $inventories[0]->setOnHand(10);
        $inventories[0]->setSafetyStock(2);

        $this->sdk->getCatalogService()->addInventories($inventories, ['requestType' => 'direct']);
        $inventory = new Inventory\Create($inventoryData);

        // Act
        $this->sdk->getCatalogService()->updateInventories([$inventory], ['requestType' => 'direct']);

        // Assert
        $product = $this->sdk->getCatalogService()->getProduct(self::PRODUCT_CODE, ['fields' => 'inventory']);

        $inventory        = $product->inventory;
        $currentInventory = new Inventory($inventory[0]);

        $this->assertEquals($expectedOnHand, $currentInventory->onHand);
        $this->assertEquals($expectedAvailable, $currentInventory->available);
        $this->assertEquals($expectedSafetyStock, $currentInventory->safetyStock);
        $this->assertEquals($expectedVisible, $currentInventory->visible);

        // CleanUp
        $this->deleteEntitiesAfterTestRun(
            self::CATALOG_SERVICE,
            self::METHOD_DELETE_PRODUCT,
            [self::PRODUCT_CODE]
        );
        $this->deleteLocation(self::LOCATION_CODE);
    }

    /**
     * @return array
     */
    public function provideTestInventoryCalculationWithoutSafety()
    {
        return [
            'increment 10' => [
                'inventoryData'       => [
                    'productCode'   => self::PRODUCT_CODE,
                    'locationCode'  => self::LOCATION_CODE,
                    'operationType' => Inventory\Create::OPERATION_TYPE_INCREMENT,
                    'sku'           => 'SKU_1',
                    'onHand'        => 10,
                ],
                'expectedOnHand'      => 20,
                'expectedSafetyStock' => 0,
                'expectedAvailable'   => 20,
                'expectedVisible'     => 20,
            ],
            'increment 20' => [
                'inventoryData'       => [
                    'productCode'   => self::PRODUCT_CODE,
                    'locationCode'  => self::LOCATION_CODE,
                    'operationType' => Inventory\Create::OPERATION_TYPE_INCREMENT,
                    'sku'           => 'SKU_1',
                    'onHand'        => 20,
                ],
                'expectedOnHand'      => 30,
                'expectedSafetyStock' => 0,
                'expectedAvailable'   => 30,
                'expectedVisible'     => 30,
            ],
            'decrement 2'  => [
                'inventoryData'       => [
                    'productCode'   => self::PRODUCT_CODE,
                    'locationCode'  => self::LOCATION_CODE,
                    'operationType' => Inventory\Create::OPERATION_TYPE_DECREMENT,
                    'sku'           => 'SKU_1',
                    'onHand'        => 2,
                ],
                'expectedOnHand'      => 8,
                'expectedSafetyStock' => 0,
                'expectedAvailable'   => 8,
                'expectedVisible'     => 8,
            ],
            'decrement 20' => [
                'inventoryData'       => [
                    'productCode'   => self::PRODUCT_CODE,
                    'locationCode'  => self::LOCATION_CODE,
                    'operationType' => Inventory\Create::OPERATION_TYPE_DECREMENT,
                    'sku'           => 'SKU_1',
                    'onHand'        => 20,
                ],
                'expectedOnHand'      => -10,
                'expectedSafetyStock' => 0,
                'expectedAvailable'   => 0,
                'expectedVisible'     => 0,
            ],
        ];
    }

    /**
     * @return array
     */
    public function provideTestInventoryCalculationWithSafety()
    {
        return [
            'increment 10' => [
                'inventoryData'       => [
                    'productCode'   => self::PRODUCT_CODE,
                    'locationCode'  => self::LOCATION_CODE,
                    'operationType' => Inventory\Create::OPERATION_TYPE_INCREMENT,
                    'sku'           => 'SKU_1',
                    'onHand'        => 10,
                ],
                'expectedOnHand'      => 20,
                'expectedSafetyStock' => 2,
                'expectedAvailable'   => 20,
                'expectedVisible'     => 18,
            ],
            'increment 20' => [
                'inventoryData'       => [
                    'productCode'   => self::PRODUCT_CODE,
                    'locationCode'  => self::LOCATION_CODE,
                    'operationType' => Inventory\Create::OPERATION_TYPE_INCREMENT,
                    'sku'           => 'SKU_1',
                    'onHand'        => 20,
                ],
                'expectedOnHand'      => 30,
                'expectedSafetyStock' => 2,
                'expectedAvailable'   => 30,
                'expectedVisible'     => 28,
            ],
            'decrement 2'  => [
                'inventoryData'       => [
                    'productCode'   => self::PRODUCT_CODE,
                    'locationCode'  => self::LOCATION_CODE,
                    'operationType' => Inventory\Create::OPERATION_TYPE_DECREMENT,
                    'sku'           => 'SKU_1',
                    'onHand'        => 2,
                ],
                'expectedOnHand'      => 8,
                'expectedSafetyStock' => 2,
                'expectedAvailable'   => 8,
                'expectedVisible'     => 6,
            ],
            'decrement 20' => [
                'inventoryData'       => [
                    'productCode'   => self::PRODUCT_CODE,
                    'locationCode'  => self::LOCATION_CODE,
                    'operationType' => Inventory\Create::OPERATION_TYPE_DECREMENT,
                    'sku'           => 'SKU_1',
                    'onHand'        => 20,
                ],
                'expectedOnHand'      => -10,
                'expectedSafetyStock' => 2,
                'expectedAvailable'   => 0,
                'expectedVisible'     => 0,
            ],
        ];
    }

    /**
     * @return array
     */
    public function provideCreateInventoryWithMissingRequiredFields()
    {
        return [
            'missing productCode'  => [
                'inventoryData'     => [
                    'locationCode' => self::LOCATION_CODE,
                    'sku'          => 'SKU_1',
                ],
                'expectedException' => new RequestException(400),
                'missingItem'       => 'productCode',
            ],
            'missing locationCode' => [
                'inventoryData'     => [
                    'productCode' => self::PRODUCT_CODE,
                    'sku'         => 'SKU_1',
                ],
                'expectedException' => new RequestException(400),
                'missingItem'       => 'locationCode',
            ],
            'missing sku'          => [
                'inventoryData'     => [
                    'productCode'  => self::PRODUCT_CODE,
                    'locationCode' => self::LOCATION_CODE,
                ],
                'expectedException' => new RequestException(400),
                'missingItem'       => 'sku',
            ],
        ];
    }
}