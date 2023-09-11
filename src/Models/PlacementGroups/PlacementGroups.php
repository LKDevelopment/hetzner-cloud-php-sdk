<?php

namespace LKDev\HetznerCloud\Models\PlacementGroups;

use LKDev\HetznerCloud\APIResponse;
use LKDev\HetznerCloud\HetznerAPIClient;
use LKDev\HetznerCloud\Models\Contracts\Resources;
use LKDev\HetznerCloud\Models\Meta;
use LKDev\HetznerCloud\Models\Model;
use LKDev\HetznerCloud\Models\PlacementGroups\PlacementGroupRequestOpts;
use LKDev\HetznerCloud\RequestOpts;
use LKDev\HetznerCloud\Traits\GetFunctionTrait;

class PlacementGroups extends Model implements Resources
{

    use GetFunctionTrait;

    /**
     * @var array
     */
    protected $placement_groups;

    /**
     * Returns all existing placementGroup objects.
     *
     * @see https://docs.hetzner.cloud/#placement-groups-get-all-placementgroups
     *
     * @param  RequestOpts|null  $requestOpts
     * @return array
     *
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function all(RequestOpts $requestOpts = null): array
    {
        if ($requestOpts == null) {
            $requestOpts = new PlacementGroupRequestOpts();
        }

        return $this->_all($requestOpts);
    }

    /**
     * Returns all existing PlacementGroup objects.
     *
     * @see https://docs.hetzner.cloud/#placement-groups-get-all-placementgroups
     *
     * @param  RequestOpts|null  $requestOpts
     * @return APIResponse|null
     *
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function list(RequestOpts $requestOpts = null): ?APIResponse
    {
        if ($requestOpts == null) {
            $requestOpts = new PlacementGroupRequestOpts();
        }
        $response = $this->httpClient->get('placement_groups'.$requestOpts->buildQuery());
        if (! HetznerAPIClient::hasError($response)) {
            $resp = json_decode((string) $response->getBody());

            return APIResponse::create([
                'meta' => Meta::parse($resp->meta),
                $this->_getKeys()['many' ]=> self::parse($resp->{$this->_getKeys()['many']})->{$this->_getKeys()['many']},
            ], $response->getHeaders());
        }

        return null;
    }

    /**
     * Returns a specific PlacementGroup object. The PlacementGroup must exist inside the project.
     *
     * @see https://docs.hetzner.cloud/#placement-groups-get-a-placementgroup
     *
     * @param  int  $serverId
     * @return PlacementGroup
     *
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function getById(int $serverId): ?PlacementGroup
    {
        $response = $this->httpClient->get('placement_group/'.$serverId);
        if (! HetznerAPIClient::hasError($response)) {
            return PlacementGroup::parse(json_decode((string) $response->getBody())->network);
        }

        return null;
    }

    /**
     * Returns a specific placementGroup object by its name. The placementGroup must exist inside the project.
     *
     * @see https://docs.hetzner.cloud/#placement-groups-get-all-placementgroups
     *
     * @param  string  $name
     * @return PlacementGroup|null
     *
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function getByName(string $name): ?PlacementGroup
    {
        $placementGroups = $this->list(new NetworkRequestOpts($name));

        return (count($placementGroups->placement_groups) > 0) ? $placementGroups->placement_groups[0] : null;
    }

    /**
     * @param  $input
     * @return $this
     */
    public function setAdditionalData($input)
    {
        $this->placement_groups = collect($input)
            ->map(function ($placementGroup) {
                if ($placementGroup != null) {
                    return PlacementGroup::parse($placementGroup);
                }
            })
            ->toArray();

        return $this;
    }

    /**
     * @param  string  $name
     * @param  string  $type
     * @param  array  $labels
     */
    public function create(string $name, string $type, array $labels = [])
    {
        $payload = [
            'name' => $name,
            'type' => $type,
        ];
        if (! empty($labels)) {
            $payload['labels'] = $labels;
        }

        $response = $this->httpClient->post('placement_groups', [
            'json' => $payload,
        ]);
        if (! HetznerAPIClient::hasError($response)) {
            $payload = json_decode((string) $response->getBody());

            return APIResponse::create([
                'placement_group' => PlacementGroup::parse($payload->placement_group),
            ], $response->getHeaders());
        }
    }

    /**
     * @param  $input
     * @return static
     */
    public static function parse($input)
    {
        return (new self())->setAdditionalData($input);
    }

    /**
     * @return array
     */
    public function _getKeys(): array
    {
        return ['one' => 'placementgroup', 'many' => 'placementgroups'];
    }

}