<?php

namespace App\Http\Controllers;

use App\Models\Order;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class DropdownController extends Controller
{
    public function index(Request $request)
    {
        $dropdownNames = trim($request->input('dropdown_names'));

        //Split dropdown names
        $dropdownNames = explode(",", $dropdownNames);

        //response to send
        $response = [];

        for ($i = 0; $i < count($dropdownNames); $i++) {

            //Get the function name
            $functionName = (string)$dropdownNames[$i];

            //check if drodown name function exist
            if (method_exists(DropdownController::class, $functionName)) {

                //get data from that function
                $dropdownData = $this->$functionName($request);

                // if (count($dropdownData) > 0) {
                //set all the data in an object
                $dropdownObject = (object)[];
                $dropdownObject->$functionName = $dropdownData;

                //push into response
                array_push($response, $dropdownObject);
                // }
            }
        }

        //return response
        return wt_api_json_success($response);
    }
    public function countries(Request $request)
    {
        $token = $this->getCountryApiAccessToken();

        $client = new Client();

        // Define the request options
        $options = [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ],
        ];

        // Make a GET request to the API endpoint
        $response = $client->request('GET', 'https://www.universal-tutorial.com/api/countries/', $options);

        // Get the response body as a string
        $responseBody = $response->getBody()->getContents();

        // Print the response
        $countries = json_decode($responseBody);

        return $countries;
        // return wt_api_json_success($countries['original']);
    }

    public function states(Request $request)
    {
        $country_name = $request->input('country_name');
        $token = $this->getCountryApiAccessToken();

        $client = new Client();

        // Define the request options
        $options = [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ],
        ];

        $state_url = 'https://www.universal-tutorial.com/api/states/' . $country_name;
        // Make a GET request to the API endpoint
        $response = $client->request('GET', $state_url, $options);

        // Get the response body as a string
        $responseBody = $response->getBody()->getContents();

        // Print the response
        $states = json_decode($responseBody);

        return $states;
        // return wt_api_json_success($countries['original']);
    }

    public function cities(Request $request)
    {
        $state_name = $request->input('state_name');
        $token = $this->getCountryApiAccessToken();

        $client = new Client();

        // Define the request options
        $options = [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ],
        ];

        $city_url = 'https://www.universal-tutorial.com/api/cities/' . $state_name;
        // Make a GET request to the API endpoint
        $response = $client->request('GET', $city_url, $options);

        // Get the response body as a string
        $responseBody = $response->getBody()->getContents();

        // Print the response
        $cities = json_decode($responseBody);

        return $cities;
        // return wt_api_json_success($countries['original']);
    }

    private function getCountryApiAccessToken()
    {
        $apiToken = "EDusYkVYnQD4Bb7dW2zFJEPQp1ziUqkO6LM4OQYvQ_0d70YZpv9KVtDjgMzB_6R8s-Y";
        $userEmail = "shaheerzaeem26@gmail.com";

        // Create a Guzzle HTTP client
        $client = new Client();

        // Define the request options
        $options = [
            'headers' => [
                'Accept' => 'application/json',
                'api-token' => $apiToken,
                'user-email' => $userEmail,
            ],
        ];

        // Make a GET request to the API endpoint
        $response = $client->request('GET', 'https://www.universal-tutorial.com/api/getaccesstoken', $options);

        // Get the response body as a string
        $responseBody = $response->getBody()->getContents();

        $token = json_decode($responseBody);
        return $token->auth_token;
    }

    public function order_status(Request $request)
    {
        return Order::STATUS_OBJECT;
    }
}


//working fine now
