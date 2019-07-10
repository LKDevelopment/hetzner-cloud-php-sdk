<?php


namespace LKDev\HetznerCloud\Models\Networks;


use LKDev\HetznerCloud\APIResponse;
use LKDev\HetznerCloud\Clients\GuzzleClient;
use LKDev\HetznerCloud\HetznerAPIClient;
use LKDev\HetznerCloud\Models\Actions\Action;
use LKDev\HetznerCloud\Models\Model;

/**
 * Class Subnet
 * @package LKDev\HetznerCloud\Models\Networks
 */
class Subnet extends Model
{
    /**
     *
     */
    const TYPE_SERVER = "server";
    /**
     * @var string
     */
    public $type;
    /**
     * @var string
     */
    public $ipRange;
    /**
     * @var string
     */
    public $networkZone;
    /**
     * @var string
     */
    public $gateway;

    /**
     * Subnet constructor.
     * @param string $type
     * @param string $ipRange
     * @param string $networkZone
     * @param string $gateway
     * @param GuzzleClient|null $client
     */
    public function __construct(string $type, string $ipRange, string $networkZone, string $gateway = null, GuzzleClient $client = null)
    {
        $this->type = $type;
        $this->ipRange = $ipRange;
        $this->networkZone = $networkZone;
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
        return collect($input)->map(function ($subnet) use ($client) {
            return new Subnet($subnet->type, $subnet->ip_range, $subnet->network_zone, $subnet->gateway, $client);
        })->toArray();
    }

    /**
     * @return array
     */
    public function __toRequestPayload(){
        return [
            "type" => $this->type,
            "ip_range" => $this->ipRange,
            "network_zone" => $this->networkZone,
        ];
    }

}
