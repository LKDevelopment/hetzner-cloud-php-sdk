<?php

namespace LKDev\HetznerCloud\Models\PlacementGroups;

use LKDev\HetznerCloud\Clients\GuzzleClient;
use LKDev\HetznerCloud\APIResponse;
use LKDev\HetznerCloud\HetznerAPIClient;
use LKDev\HetznerCloud\Models\Actions\Action;
use LKDev\HetznerCloud\Models\Contracts\Resource;
use LKDev\HetznerCloud\Models\Model;
use LKDev\HetznerCloud\Models\Servers\Server;

/**
 * Class PlacementGroup.
 */
class PlacementGroup extends Model implements Resource
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var array
     */
    public $labels;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $type;

    /**
     * @var array
     */
    public $servers;

    /**
     * @var string
     */
    public $created;

    /**
     * PlacementGroup constructor.
     *
     * @param  int  $id
     * @param  GuzzleClient|null  $httpClient
     */
    public function __construct(int $id, ?GuzzleClient $httpClient = null)
    {
        $this->id = $id;
        parent::__construct($httpClient);
    }

    /**
     * @param  $data
     * @return $this
     */
    private function setAdditionalData($data)
    {
        $this->name = $data->name;
        $this->type = $data->type;
        $this->servers = collect($data->servers)
            ->map(function ($id) {
                return new Server($id);
            })->toArray();

        $this->labels = get_object_vars($data->labels);
        $this->created = $data->created;

        return $this;
    }

    /**
     * @param  $input
     * @return static
     */
    public static function parse($input)
    {
        return (new self($input->id))->setAdditionalData($input);
    }

    public function reload()
    {
        return HetznerAPIClient::$instance->placementGroups()->get($this->id);
    }

    public function delete()
    {
        $response = $this->httpClient->delete('placement_groups/'.$this->id);
        if (! HetznerAPIClient::hasError($response)) {
            return APIResponse::create([
                'action' => Action::parse(json_decode((string) $response->getBody())->action),
            ], $response->getHeaders());
        }
    }

    public function update(array $data)
    {
        $response = $this->httpClient->put('placement_groups/'.$this->id, [
            'json' => $data,
        ]);
        if (! HetznerAPIClient::hasError($response)) {
            return APIResponse::create([
                'placement_group' => self::parse(json_decode((string) $response->getBody())->network),
            ], $response->getHeaders());
        }

        return null;
    }
}
