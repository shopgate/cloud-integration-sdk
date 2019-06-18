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

use Shopgate\ConnectSdk\ClientInterface;
use Shopgate\ConnectSdk\Exception\RequestException;
use Shopgate\ConnectSdk\Exception\UnknownException;
use Shopgate\ConnectSdk\Service\BulkImport\Handler\File;
use Shopgate\ConnectSdk\Service\BulkImport\Handler\Stream;

class BulkImport
{
    /** @var ClientInterface */
    private $client;

    /**
     * @param ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @return mixed
     * @throws RequestException
     * @throws UnknownException
     */
    protected function getImportReference()
    {
        $response = $this->client->doRequest(
            [
                'method'      => 'post',
                'body'        => [],
                'requestType' => 'direct',
                'service'     => 'import',
                'path'        => 'imports',
            ]
        );

        $response = json_decode($response->getBody(), true);

        return $response['ref'];
    }

    /**
     * @return File
     * @throws RequestException
     * @throws UnknownException
     */
    public function createFileImport()
    {
        return new File($this->client, $this->getImportReference());
    }

    /**
     * @return Stream
     * @throws RequestException
     * @throws UnknownException
     */
    public function createStreamImport()
    {
        return new Stream($this->client, $this->getImportReference());
    }
}
