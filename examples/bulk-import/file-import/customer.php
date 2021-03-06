<?php

require_once(dirname(__FILE__) . '/../../bootstrap.php');

try {
    $customers = provideSampleCustomers();

    $handler = $sdk->getBulkImportService()->createFileImport();
    $customerHandler = $handler->createCustomerFeed();
    $customerHandler->add($customers[0]);
    $customerHandler->end();
    $handler->trigger();
} catch (Exception $exception) {
    echo $exception->getMessage();
}
