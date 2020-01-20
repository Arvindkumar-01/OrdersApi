<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Response;

class GoogleRoutes
{
    private $api_url = 'https://maps.googleapis.com/maps/api/distancematrix/json';
    private $type = 'GET';
    private $api_success_status = "OK";

    public function prepairParameters($params)
    {
        $origins = implode(',', $params['origin']);
        $destinations = implode(',', $params['destination']);
        return [
            'query' => [
                'origins' => $origins,
                'destinations' => $destinations,
                'key' => config('constants.google_map_api_key'),
                'unit' => 'metric'
            ],
        ];
    }

    public function sendRequest($data)
    {
        $client = new Client();
        try {
            $this->response = $client->request(
                $this->type,
                $this->api_url,
                $this->prepairParameters($data)
            );

            $response = [
                'http_status_code' => $this->response->getStatusCode(),
                'data'             => $this->response->getBody()->getContents()
            ];
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $response = [
                'http_status_code' => $e->getCode(),
                'error'            => $e->getMessage(),
                'response'         => $e->getResponse()
            ];
        } catch (\Exception $e) {
            $response = [
                'http_status_code' => $e->getCode(),
                'error' => $e->getMessage()
            ];
        }
        Log::info('--------- Guzzle Http Response ------');
        Log::info($response);
        return $response;
    }

    public function calculate_distance($data)
    {
        $dist_not_found = __('message.distance_not_found');
        $response = $this->sendRequest($data);
        if ($response['http_status_code'] == Response::HTTP_OK) {

            $total_distance = 0;
            $res_data = json_decode($response['data'], true);
            if ($res_data['status'] != $this->api_success_status) {
                return [
                    'status' => false,
                    'error_code' => Response::HTTP_NOT_FOUND,
                    'error' => isset($res_data['error_message']) ? $res_data['error_message'] : $dist_not_found
                ];
            }
            foreach ($res_data['rows'] as $rows) {
                foreach ($rows['elements'] as $elements) {

                    if ($elements['status'] != $this->api_success_status) {
                        return [
                            'status' => false,
                            'error_code' => Response::HTTP_NOT_FOUND,
                            'error' => $dist_not_found
                        ];
                    }
                    $total_distance = isset($elements['distance']['value']) ? $elements['distance']['value'] : 0;
                }
            }
            return [
                'status' => true,
                'total_distance' => $total_distance
            ];
        } else {
            return [
                'status' => false,
                'error_code' => $response['http_status_code'],
                'error' => $response['error']
            ];
        }
    }
}
