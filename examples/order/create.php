<?php

require_once(dirname(__FILE__) . '/../bootstrap.php');

$orders = provideSampleOrders();

try {
    $sgSdk->getOrderService()->addOrders($orders);
} catch (Exception $exception) {
    echo $exception->getMessage();
}
