<?php
/**
 * Created by PhpStorm.
 * User: lukaskammerling
 * Date: 28.01.18
 * Time: 21:00.
 */

namespace LKDev\HetznerCloud\Models\Certificates;

use LKDev\HetznerCloud\APIResponse;
use LKDev\HetznerCloud\HetznerAPIClient;
use LKDev\HetznerCloud\Models\Contracts\Resources;
use LKDev\HetznerCloud\Models\Meta;
use LKDev\HetznerCloud\Models\Model;
use LKDev\HetznerCloud\RequestOpts;
use LKDev\HetznerCloud\Traits\GetFunctionTrait;

class Certificates extends Model implements Resources
{
    use GetFunctionTrait;

    /**
     * @var array
     */
    protected $certificates;

    /**
     * Creates a new SSH Key with the given name and certificate.
     *
     * @see https://docs.hetzner.cloud/#certificates-create-a-certificate
     * @param string $name
     * @param string $certificate
     * @param string $privateKey
     * @param array $labels
     * @return \LKDev\HetznerCloud\Models\Certificates\Certificate
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function create(
        string $name,
        string $certificate,
        string $privateKey,
        array $labels = []
    ): ?Certificate {
        $parameters = [
            'name' => $name,
            'certificate' => $certificate,
            'private_key' => $privateKey,
        ];
        if (! empty($labels)) {
            $parameters['labels'] = $labels;
        }
        $response = $this->httpClient->post('certificates', [
            'json' => $parameters,
        ]);
        if (! HetznerAPIClient::hasError($response)) {
            return Certificate::parse(json_decode((string) $response->getBody())->certificate);
        }

        return null;
    }

    /**
     * Returns all certificate objects.
     *
     * @see https://docs.hetzner.cloud/#certificates-get-all-certificates
     * @param RequestOpts $requestOpts
     * @return array
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function all(RequestOpts $requestOpts = null): array
    {
        if ($requestOpts == null) {
            $requestOpts = new RequestOpts();
        }

        return $this->_all($requestOpts);
    }

    /**
     * Returns all certificate objects.
     *
     * @see https://docs.hetzner.cloud/#certificates-get-all-certificates
     * @param RequestOpts $requestOpts
     * @return APIResponse
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function list(RequestOpts $requestOpts = null): ?APIResponse
    {
        if ($requestOpts == null) {
            $requestOpts = new RequestOpts();
        }
        $response = $this->httpClient->get('certificates'.$requestOpts->buildQuery());
        if (! HetznerAPIClient::hasError($response)) {
            $resp = json_decode((string) $response->getBody());

            return APIResponse::create([
                'meta' => Meta::parse($resp->meta),
                $this->_getKeys()['many'] => self::parse($resp->{$this->_getKeys()['many']})->{$this->_getKeys()['many']},
            ], $response->getHeaders());
        }

        return null;
    }

    /**
     * @param  $input
     * @return $this
     */
    public function setAdditionalData($input)
    {
        $this->certificates = collect($input)->map(function ($certificate, $key) {
            return Certificate::parse($certificate);
        })->toArray();

        return $this;
    }

    /**
     * @param  $input
     * @return $this|static
     */
    public static function parse($input)
    {
        return (new self())->setAdditionalData($input);
    }

    /**
     * Returns a specific certificate object.
     *
     * @see https://docs.hetzner.cloud/#certificates-get-a-certificate
     * @param int $id
     * @return \LKDev\HetznerCloud\Models\Certificates\Certificate
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function getById(int $id)
    {
        $response = $this->httpClient->get('certificates/'.$id);
        if (! HetznerAPIClient::hasError($response)) {
            return Certificate::parse(json_decode((string) $response->getBody())->{$this->_getKeys()['one']});
        }
    }

    /**
     * Returns a specific certificate object.
     *
     * @see https://docs.hetzner.cloud/#certificates-get-a-certificate
     * @param string $name
     * @return \LKDev\HetznerCloud\Models\Certificates\Certificate
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function getByName(string $name): ?Certificate
    {
        $certificates = $this->list(new CertificateRequestOpts($name));

        return (count($certificates->{$this->_getKeys()['many']}) > 0) ? $certificates->{$this->_getKeys()['many']}[0] : null;
    }

    /**
     * @return array
     */
    public function _getKeys(): array
    {
        return ['one' => 'certificate', 'many' => 'certificates'];
    }
}
