<?php
/**
 * Created by PhpStorm.
 * User: lukaskammerling
 * Date: 28.01.18
 * Time: 21:02
 */

namespace LKDev\HetznerCloud\Models\ISOs;

use LKDev\HetznerCloud\HetznerAPIClient;
use LKDev\HetznerCloud\Models\Contracts\Resources;
use LKDev\HetznerCloud\Models\Model;
use LKDev\HetznerCloud\RequestOpts;
use LKDev\HetznerCloud\Traits\GetFunctionTrait;

class ISOs extends Model implements Resources
{
    use GetFunctionTrait;
    /**
     * @var array
     */
    public $isos;

    /**
     * Returns all iso objects.
     *
     * @see https://docs.hetzner.cloud/#resources-isos-get
     * @param RequestOpts $requestOpts
     * @return array
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function all(RequestOpts $requestOpts = null): array
    {
        if ($requestOpts == null) {
            $requestOpts = new RequestOpts();
        }
        $isos = [];
        $requestOpts->per_page = HetznerAPIClient::MAX_ENTITIES_PER_PAGE;
        for ($i = 1; $i < PHP_INT_MAX; $i++) {
            $_s = $this->list($requestOpts);
            $isos = array_merge($isos, $_s);
            if (empty($_s)) {
                break;
            }
        }
        return $isos;
    }
    /**
     * Returns all iso objects.
     *
     * @see https://docs.hetzner.cloud/#resources-isos-get
     * @param RequestOpts $requestOpts
     * @return array
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function list(RequestOpts $requestOpts = null): array
    {
        if ($requestOpts == null) {
            $requestOpts = new RequestOpts();
        }
        $response = $this->httpClient->get('isos' . $requestOpts->buildQuery());
        if (!HetznerAPIClient::hasError($response)) {
            return self::parse(json_decode((string)$response->getBody()))->isos;
        }
    }
    /**
     * Returns a specific iso object.
     *
     * @see https://docs.hetzner.cloud/#resources-iso-get-1
     * @param int $isoId
     * @return \LKDev\HetznerCloud\Models\ISOs\ISO
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function getById(int $isoId): ISO
    {
        $response = $this->httpClient->get('isos/' . $isoId);
        if (!HetznerAPIClient::hasError($response)) {
            return ISO::parse(json_decode((string)$response->getBody())->iso);
        }
    }
    /**
     * Returns a specific iso object by its name
     *
     * @see https://docs.hetzner.cloud/#resources-iso-get-1
     * @param int $isoId
     * @return \LKDev\HetznerCloud\Models\ISOs\ISO
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function getByName(string $name): ISO
    {
        $isos = $this->all(new ISORequestOpts($name));

        return (count($isos) > 0) ? $isos[0] : null;
    }
    /**
     * @param  $input
     * @return $this
     */
    public function setAdditionalData($input)
    {
        $this->isos = collect($input->isos)->map(function ($iso, $key) {
            return ISO::parse($iso);
        })->toArray();

        return $this;
    }

    /**
     * @param $input
     * @return $this|static
     */
    public static function parse($input)
    {
        return (new self())->setAdditionalData($input);
    }
}
