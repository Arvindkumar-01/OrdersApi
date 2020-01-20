<?php

namespace Tests\Unit;

use App\Http\Controllers\Api\OrderController;
use App\Http\Requests\ListOrder;
use App\Http\Requests\StoreOrder;
use App\Http\Requests\UpdateOrder;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;

class OrderTest extends TestCase
{
    public function setUp(): void
    {
        parent::setup();
        $this->mock = \Mockery::mock('App\Repositories\OrderRepository');
        $this->orderController = new OrderController($this->mock);
    }

    public function testSuccessOrderCreation()
    {
        $params = [
            "origin" => ["40.6655101", "-93.89188969999998"],
            "destination" => ["40.6905615", "-73.9976592"]
        ];
        $output = [
            'status' => true,
            'data' => [
                "id" => 18,
                "distance" => 1909826,
                "status" => "UNASSIGNED"
            ]
        ];

        $request = new StoreOrder($params);
        $this->mock->shouldReceive('storeOrder')->andReturn($output);
        $response =  $this->orderController->store($request);
        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('id', $data);
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @dataProvider provideOrderData
     */
    public function testFailureOrderCreation($input, $eror_msg, $error_code)
    {
        $params =  $input;

        $output = [
            'status' => false,
            'data' => [
                'error' => $eror_msg
            ]
        ];
        // dd($params);
        $request = new StoreOrder($params);
        $this->mock->shouldReceive('storeOrder')->andReturn($output);
        $response =  $this->orderController->store($request);
        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('error', $data);
        $this->assertEquals($error_code, $response->getStatusCode());
    }

    public function provideOrderData()
    {
        return [
            [['origin' => ["40.6655101", "0"], 'destination' => ["0", "0"]], "Error Message", 422],
            [['origin' => ["40.6655101", "-93.89188969999998"], 'destination' => ["40.6905615", "-73.9976592", 0]], "The destination must contain 2 items.", 422],
            [['origin' => ["140.6655101", "-93.89188969999998"], 'destination' => ["40.6905615", "-73.9976592"]], "origin is a valid Lat Long", 422],
        ];
    }
    public function testSuccessOrderList()
    {
        $params = [
            "limit" => 5,
            "page" => 1
        ];
        $output = [
            [
                "id" => 18,
                "distance" => 1909826,
                "status" => "UNASSIGNED"
            ]
        ];

        $request = new ListOrder($params);
        $this->mock->shouldReceive('list')->andReturn($output);
        $response =  $this->orderController->list($request);

        $data = json_decode($response->getContent(), true);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testSuccessOrderUpdate()
    {
        $params = ['status' => 'TAKEN'];
        $id = 1;
        $request = new UpdateOrder($params);
        $this->mock->shouldReceive('updateOrder')->andReturn(['status' => true, 'data' => ['status' => 'SUCCESS']]);
        $response =  $this->orderController->update($request, $id);

        $data = json_decode($response->getContent(), true);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('status', $data);
    }
    /**
     * @dataProvider provideOrderUpdateData
     */
    public function testFailOrderUpdate($id, $input, $error, $status_code)
    {
        $params = ['status' => $input];

        $output = ['status' => false, 'error' => $error];
        $request = new UpdateOrder($params);
        $this->mock->shouldReceive('updateOrder')->andReturn($output);
        $response =  $this->orderController->update($request, $id);

        $data = json_decode($response->getContent(), true);
        $this->assertEquals($status_code, $response->getStatusCode());
    }

    public function provideOrderUpdateData()
    {
        return [
            [1, '', 'Status is required', 422],
            [1, 'XYZ', 'Status is invalid', 422],
            [0, 'TAKEN', 'Not Found', 422]
        ];
    }


    public function tearDown(): void
    {
        \Mockery::close();
        parent::tearDown();
    }
}
