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

namespace Shopgate\ConnectSdk\Service;

use Psr\Http\Message\ResponseInterface;
use Shopgate\ConnectSdk\Dto\Location\Location as LocationDto;
use Shopgate\ConnectSdk\Dto\Meta;
use Shopgate\ConnectSdk\Exception\AuthenticationInvalidException;
use Shopgate\ConnectSdk\Exception\InvalidDataTypeException;
use Shopgate\ConnectSdk\Exception\NotFoundException;
use Shopgate\ConnectSdk\Exception\RequestException;
use Shopgate\ConnectSdk\Exception\UnknownException;
use Shopgate\ConnectSdk\Http\ClientInterface;
use Shopgate\ConnectSdk\ShopgateSdk;
use Shopgate\ConnectSdk\Helper\Json;

class Location
{
    const SERVICE_LOCATION = 'location';

    /** @var ClientInterface */
    private $client;

    /** @var Json */
    private $jsonHelper;

    /**
     * @param ClientInterface $client
     * @param Json            $jsonHelper
     */
    public function __construct(ClientInterface $client, Json $jsonHelper)
    {
        $this->client     = $client;
        $this->jsonHelper = $jsonHelper;
    }

    /**
     * @param LocationDto\Create[] $locations
     * @param array                $query
     *
     * @return ResponseInterface
     *
     * @throws AuthenticationInvalidException
     * @throws NotFoundException
     * @throws RequestException
     * @throws UnknownException
     * @throws InvalidDataTypeException
     */
    public function addLocations(array $locations, array $query = [])
    {
        return $this->client->doRequest(
            [
                // general
                'method'      => 'post',
                'requestType' => ShopgateSdk::REQUEST_TYPE_DIRECT,
                'json'        => ['locations' => $locations],
                'query'       => $query,
                // direct
                'service'     => self::SERVICE_LOCATION,
                'path'        => 'locations'
            ]
        );
    }

    /**
     * @param string             $code
     * @param LocationDTO\Update $location
     * @param array              $query
     *
     * @return ResponseInterface
     *
     * @throws AuthenticationInvalidException
     * @throws NotFoundException
     * @throws RequestException
     * @throws UnknownException
     * @throws InvalidDataTypeException
     */
    public function updateLocation($code, LocationDto\Update $location, array $query = [])
    {
        return $this->client->doRequest(
            [
                // general
                'requestType' => ShopgateSdk::REQUEST_TYPE_DIRECT,
                'json'        => $location,
                'query'       => $query,
                // direct
                'method'      => 'post',
                'service'     => self::SERVICE_LOCATION,
                'path'        => 'locations/' . $code
            ]
        );
    }

    /**
     * @param string $code
     * @param array  $query
     *
     * @return ResponseInterface
     *
     * @throws AuthenticationInvalidException
     * @throws NotFoundException
     * @throws RequestException
     * @throws UnknownException
     * @throws InvalidDataTypeException
     */
    public function deleteLocation($code, array $query = [])
    {
        return $this->client->doRequest(
            [
                // general
                'requestType' => ShopgateSdk::REQUEST_TYPE_DIRECT,
                'query'       => $query,
                // direct
                'method'      => 'delete',
                'service'     => self::SERVICE_LOCATION,
                'path'        => 'locations/' . $code,
            ]
        );
    }

    /**
     * @param array $query
     *
     * @return LocationDto\GetList
     *
     * @throws AuthenticationInvalidException
     * @throws NotFoundException
     * @throws RequestException
     * @throws UnknownException
     * @throws InvalidDataTypeException
     */
    public function getLocations(array $query = [])
    {
        if (isset($query['filters'])) {
            $query['filters'] = $this->jsonHelper->encode($query['filters']);
        }

        $response = $this->client->doRequest(
            [
                // direct only
                'service' => self::SERVICE_LOCATION,
                'method'  => 'get',
                'path'    => 'locations',
                'query'   => $query,
            ]
        );
        $response = $this->jsonHelper->decode($response->getBody(), true);

        $locations = [];
        foreach ($response['locations'] as $location) {
            $locations[] = new LocationDto\Get($location);
        }
        $response['meta']       = new Meta($response['meta']);
        $response['locations'] = $locations;

        return new LocationDto\GetList($response);
    }

    /**
     * @param string $code location code
     * @param array  $query
     *
     * @return LocationDto\Get
     *
     * @throws AuthenticationInvalidException
     * @throws NotFoundException
     * @throws RequestException
     * @throws UnknownException
     * @throws InvalidDataTypeException
     */
    public function getLocation($code, array $query = [])
    {
        $response = $this->client->doRequest(
            [
                // direct only
                'service' => self::SERVICE_LOCATION,
                'method'  => 'get',
                'path'    => 'locations/' . $code,
                'query'   => $query,
            ]
        );

        $response = $this->jsonHelper->decode($response->getBody(), true);

        return new LocationDto\Get($response['location']);
    }
}
