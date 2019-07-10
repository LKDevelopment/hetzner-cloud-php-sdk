<?php


namespace LKDev\HetznerCloud\Models\Networks;


use LKDev\HetznerCloud\Clients\GuzzleClient;
use LKDev\HetznerCloud\Models\Model;

/**
 * Class Route
 * @package LKDev\HetznerCloud\Models\Networks
 */
class Route extends Model
{
    /**
     * @var string
     */
    public $destination;
    /**
     * @var string
     */
    public $gateway;


    /**
     * Subnet constructor.
     * @param string $destination
     * @param string $gateway
     * @param GuzzleClient|null $client
     */
    public function __construct(string $destination, string $gateway, GuzzleClient $client = null)
    {
        $this->destination = $destination;
        $this->gateway = $gateway;
        parent::__construct($client);
    }

    /**
     * @param $input
     * @param GuzzleClient|null $client
     * @return array|Model
     */
    public static function parse($input, GuzzleClient $client = null)
    {
        return collect($input)->map(function ($route) use ($client) {
            return new Route($route->destination, $route->gateway, $client);
        })->toArray();
    }

    /**
     * @return array
     */
    public function __toRequestPayload(){
        return [
            "destination" => $this->destination,
            "gateway" => $this->gateway,
        ];
    }

}
