<?php
/**
 * Created by PhpStorm.
 * User: lukaskammerling
 * Date: 28.01.18
 * Time: 21:02.
 */

namespace LKDev\HetznerCloud\Models\ISOs;

use LKDev\HetznerCloud\APIResponse;
use LKDev\HetznerCloud\HetznerAPIClient;
use LKDev\HetznerCloud\Models\Contracts\Resources;
use LKDev\HetznerCloud\Models\Meta;
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
            $requestOpts = new ISORequestOpts();
        }

        return $this->_all($requestOpts);
    }

    /**
     * Returns all iso objects.
     *
     * @see https://docs.hetzner.cloud/#resources-isos-get
     * @param RequestOpts $requestOpts
     * @return APIResponse
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function list(RequestOpts $requestOpts = null): APIResponse
    {
        if ($requestOpts == null) {
            $requestOpts = new RequestOpts();
        }
        $response = $this->httpClient->get('isos'.$requestOpts->buildQuery());
        if (! HetznerAPIClient::hasError($response)) {
            $resp = json_decode((string) $response->getBody());

            return APIResponse::create([
                'meta' => Meta::parse($resp->meta),
                $this->_getKeys()['many'] => self::parse($resp->{$this->_getKeys()['many']})->{$this->_getKeys()['many']},
            ], $response->getHeaders());
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
        $response = $this->httpClient->get('isos/'.$isoId);
        if (! HetznerAPIClient::hasError($response)) {
            return ISO::parse(json_decode((string) $response->getBody())->iso);
        }
    }

    /**
     * Returns a specific iso object by its name.
     *
     * @see https://docs.hetzner.cloud/#resources-iso-get-1
     * @param int $isoId
     * @return \LKDev\HetznerCloud\Models\ISOs\ISO
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function getByName(string $name): ?ISO
    {
        $isos = $this->list(new ISORequestOpts($name));

        return (count($isos) > 0) ? $isos[0] : null;
    }

    /**
     * @param  $input
     * @return $this
     */
    public function setAdditionalData($input)
    {
        $this->isos = collect($input)->map(function ($iso, $key) {
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

    /**
     * @return array
     */
    public function _getKeys(): array
    {
        return ['one' => 'iso', 'many' => 'isos'];
    }
}
