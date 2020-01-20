<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class OrderTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Feature test for success order creation
     *
     * @return void
     */
    public function testSuccessOrderCreation()
    {
        $inputData = [
            "origin" => [
                "40.6655101",
                "-93.89188969999998",
            ],
            "destination" => [
                "40.6905615",
                "-73.9976592"
            ]
        ];

        $response = $this->json('POST', '/api/v1/orders', $inputData);
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @dataProvider orderCreateDataProvider 
     */
    public function testFailureOrderCreation($input)
    {
        $response = $this->json('POST', '/api/v1/orders', $input);
        $data = json_decode($response->getContent(), true);
        $this->assertEquals(422, $response->getStatusCode());
    }
    /** 
     *  Data provider
     */
    public function orderCreateDataProvider()
    {
        return [
            [
                'origin' => [],
                "destination" => ["40.6905", "-73.9976"]
            ],
            [
                'origin' => ["40.6655", "-93.8918"],
                "destination" => []
            ],
            [
                'origin' => ["40.6655101"],
                "destination" => ["40.6905615", "-73.9976592"]
            ],
            [
                'origin' => ["40.6655101"],
                "destination" => ["40.6905615", -73.9976592]
            ],
        ];
    }
    /**
     *  Test for successfully order list
     */
    public function testSuccessOrderList()
    {
        $response = $this->json('GET', '/api/v1/orders', ['limit' => 10, 'page' => 1]);
        $this->assertEquals(200, $response->getStatusCode());
    }
    /**
     * Test failure case of order list
     * @dataProvider orderListDataProvider
     */
    public function testFailureOrderList($params)
    {
        $response = $this->json('GET', '/api/v1/orders', $params);
        $this->assertEquals(422, $response->getStatusCode());
    }

    /**
     *  Data provider
     */
    public function orderListDataProvider()
    {
        return [
            [['limit' => 10, 'page' => 0]],
            [['limit' => 0, 'page' => 1]],
            [['limit' => '', 'page' => '']],
            [['limit' => 10, 'page' => 'xyz']],
            [['limit' => 'xyz', 'page' => 1]],
        ];
    }
    /**
     *  Test for Successfully Order update 
     */
    public function testSuccessOrderUpdate()
    {
        // generete a new order id
        $inputData = [
            "origin" => [
                "40.6655101",
                "-93.89188969999998",
            ],
            "destination" => [
                "40.6905615",
                "-73.9976592"
            ]
        ];

        $response = $this->json('POST', '/api/v1/orders', $inputData);
        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $orderId = $data['id'];
        // Test for update order 
        $input = ['status' => 'TAKEN'];
        $response = $this->json('patch', '/api/v1/orders/' . $orderId, $input);
        $this->assertEquals(200, $response->getStatusCode());
        $response->assertJson(['status' => 'SUCCESS']);
    }
    /**
     *  Test case for Failure condition in update order
     *  @dataProvider updateOrderDataProvider
     */
    public function testFailureOrderUpdate($postdata)
    {
        $orderId = 1;
        $response = $this->json('patch', '/api/v1/orders/' . $orderId, $postdata);
        $this->assertEquals(422, $response->getStatusCode());
    }

    public function updateOrderDataProvider()
    {
        return [
            [['status' => '']],
            [['status' => 'xyz']],
            [['status' => 123]],
        ];
    }
    /**
     *  Test case for Distance not found for given Lat Long
     */
    public function testDistanceFailureOnCreateOrder()
    {
        $inputData = [
            "origin" => [
                "24.9164",
                "79.5812"
            ],
            "destination" => [
                "89.4595",
                "77.0266"
            ]
        ];

        $response = $this->json('POST', '/api/v1/orders', $inputData);
        $this->assertEquals(422, $response->getStatusCode());
        $response->assertJson(['error' => 'Distance can not calculated for given lat long.']);
    }

    /**
     *  Test case for order not found for update
     */
    public function testOrderNotFoundOnUpdate()
    {
        $orderId = 0;
        $response = $this->json('patch', '/api/v1/orders/' . $orderId, ['status' => 'TAKEN']);
        $this->assertEquals(404, $response->getStatusCode());
    }
}
