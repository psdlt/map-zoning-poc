<?php

namespace App\Service;

use App\Model\Address;
use App\Model\Point;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * https://locationiq.com/docs
 */
class LocationService
{
    private const API_KEY = '2f310ea8534500';
    private const API_ENDPOINT = 'https://eu1.locationiq.com/v1/';

    private $client;

    public function __construct(
        HttpClientInterface $client
    ) {
        $this->client = $client;
    }

    public function getPoint(Address $address): Point
    {
        // TODO: create and lookup cache first

        // do a live search
        $query = [
            // search query
            'street' => $address->house . ' ' . $address->street,
            'city' => $address->city,
            'country' => $address->country,
            'postalcode' => $address->postcode,
            // additional data
            'key' => self::API_KEY,
            'format' => 'json',
            'limit' => 1,
        ];
        $url = self::API_ENDPOINT . 'search.php?' . http_build_query($query);

        $response = $this->client->request('GET', $url);
        if ($response->getStatusCode() !== Response::HTTP_OK) {
            throw new \Exception('Invalid response status code: ' . $response->getStatusCode());
        }

        $result = $response->toArray();
        if (count($result) === 0) {
            throw new \Exception('Nothing was found');
        }

        return new Point($result[0]['lat'], $result[0]['lon']);
    }
}
