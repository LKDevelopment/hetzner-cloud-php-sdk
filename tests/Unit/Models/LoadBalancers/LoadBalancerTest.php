<?php

namespace LKDev\Tests\Unit\Models\LoadBalancers;

use GuzzleHttp\Psr7\Response;
use LKDev\HetznerCloud\Models\LoadBalancers\LoadBalancer;
use LKDev\HetznerCloud\Models\LoadBalancers\LoadBalancerHealthCheck;
use LKDev\HetznerCloud\Models\LoadBalancers\LoadBalancerHealthCheckHttp;
use LKDev\HetznerCloud\Models\LoadBalancers\LoadBalancers;
use LKDev\HetznerCloud\Models\LoadBalancers\LoadBalancerTargetIp;
use LKDev\Tests\TestCase;

/**
 * Class LoadBalancerTest.
 */
class LoadBalancerTest extends TestCase
{
    /**
     * @var LoadBalancer
     */
    protected $load_balancer;

    public function setUp(): void
    {
        parent::setUp();
        $tmp = new LoadBalancers($this->hetznerApi->getHttpClient());
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__ . '/fixtures/loadBalancer.json')));
        $this->load_balancer = $tmp->get(4711);
    }

    /**
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function testChangeName()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__ . '/fixtures/loadBalancer.json')));
        $this->assertEquals($this->load_balancer->id, 4711);
        $this->assertEquals($this->load_balancer->name, 'my-resource');

        $this->load_balancer->update(['name' => 'my-resource']);
        $this->assertLastRequestEquals('PUT', '/load_balancers/4711');
        $this->assertLastRequestBodyParametersEqual(['name' => 'my-resource']);
    }

    /**
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function testAddService()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__ . '/fixtures/loadBalancer_action_add_service.json')));

        $loadBalancerHealthCheckHttp = new LoadBalancerHealthCheckHttp(
            'example.com',
            '/',
            '{"status": "ok"}',
            [200, 300],
            false
        );

        $loadBalancerHealthCheck = new LoadBalancerHealthCheck(
            $loadBalancerHealthCheckHttp,
            15,
            4711,
            'http',
            3,
            10
        );

        $apiResponse = $this->load_balancer->addService(
            80,
            $loadBalancerHealthCheck,
            443,
            'https',
            false
        );

        $this->assertEquals('add_service', $apiResponse->action->command);
        $this->assertEquals($this->load_balancer->id, $apiResponse->action->resources[0]->id);
        $this->assertEquals('load_balancer', $apiResponse->action->resources[0]->type);

        $this->assertLastRequestEquals('POST', '/load_balancers/4711/actions/add_service');
        $this->assertLastRequestBodyParametersEqual([
            'destination_port' => 80,
            'listen_port' => 443,
            'protocol' => 'https'
        ]);
    }

    /**
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function testAddTarget()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__ . '/fixtures/loadBalancer_action_add_target.json')));

        $loadBalancerTargetIp = new LoadBalancerTargetIp(
            '1.2.3.4',
        );

        $apiResponse = $this->load_balancer->addTarget(
            'server',
            $loadBalancerTargetIp,
            true,
            ['selector' => 'env=prod']
        );

        $this->assertEquals('add_target', $apiResponse->action->command);
        $this->assertEquals($this->load_balancer->id, $apiResponse->action->resources[0]->id);
        $this->assertEquals('load_balancer', $apiResponse->action->resources[0]->type);

        $this->assertLastRequestEquals('POST', '/load_balancers/4711/actions/add_target');
        $this->assertLastRequestBodyParametersEqual([
            'type' => 'server',
            'ip' => ['ip' => '1.2.3.4'],
            'use_private_ip' => true,
            'label_selector' => ['selector' => 'env=prod'],
        ]);
    }

    /**
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function testAttachLoadBalancer()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__ . '/fixtures/loadBalancer_action_attach_loadbalancer_network.json')));

        $apiResponse = $this->load_balancer->attachLoadBalancerToNetwork(
            4711,
            '10.0.1.1'
        );

        $this->assertEquals('attach_to_network', $apiResponse->action->command);
        $this->assertEquals($this->load_balancer->id, $apiResponse->action->resources[0]->id);
        $this->assertEquals('load_balancer', $apiResponse->action->resources[0]->type);

        $this->assertLastRequestEquals('POST', '/load_balancers/4711/actions/attach_to_network');
        $this->assertLastRequestBodyParametersEqual([
            'network' => 4711,
            'ip' => '10.0.1.1',
        ]);
    }

    /**
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function testChangeAlgorithm()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__ . '/fixtures/loadBalancer_action_change_algorithm.json')));

        $apiResponse = $this->load_balancer->changeAlgorithm('round_robin');

        $this->assertEquals('change_algorithm', $apiResponse->action->command);
        $this->assertEquals($this->load_balancer->id, $apiResponse->action->resources[0]->id);
        $this->assertEquals('load_balancer', $apiResponse->action->resources[0]->type);

        $this->assertLastRequestEquals('POST', '/load_balancers/4711/actions/change_algorithm');
        $this->assertLastRequestBodyParametersEqual(['type' => 'round_robin']);
    }

    /**
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function testChangeReverseDNS()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/loadBalancer_action_change_dns_ptr.json')));
        $apiResponse = $this->load_balancer->changeReverseDnsEntry('server02.example.com', '1.2.3.4');

        $this->assertEquals('change_dns_ptr', $apiResponse->action->command);
        $this->assertEquals($this->load_balancer->id, $apiResponse->action->resources[0]->id);
        $this->assertEquals('load_balancer', $apiResponse->action->resources[0]->type);

        $this->assertLastRequestEquals('POST', '/load_balancers/4711/actions/change_dns_ptr');
        $this->assertLastRequestBodyParametersEqual(['ip' => '1.2.3.4', 'dns_ptr' => 'server02.example.com']);
    }

    /**
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function testChangeProtection()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__ . '/fixtures/loadBalancer_action_change_protection.json')));
        $apiResponse = $this->load_balancer->changeProtection(true);

        $this->assertEquals('change_protection', $apiResponse->action->command);
        $this->assertEquals($this->load_balancer->id, $apiResponse->action->resources[0]->id);
        $this->assertEquals('load_balancer', $apiResponse->action->resources[0]->type);
        $this->assertLastRequestEquals('POST', '/load_balancers/4711/actions/change_protection');
        $this->assertLastRequestBodyParametersEqual(['delete' => 1]);
    }

    /**
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function testChangeLoadBalancerType()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/loadBalancer_action_change_load_balancer_type.json')));
        $apiResponse = $this->load_balancer->changeType('lb21');

        $this->assertEquals('change_load_balancer_type', $apiResponse->action->command);
        $this->assertEquals($this->load_balancer->id, $apiResponse->action->resources[0]->id);
        $this->assertEquals('server', $apiResponse->action->resources[0]->type);

        $this->assertLastRequestEquals('POST', '/load_balancers/4711/actions/change_type');
        $this->assertLastRequestBodyParametersEqual(['load_balancer_type' => 'lb21']);
    }

    /**
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function testDeleteService()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/loadBalancer_action_delete_service.json')));
        $apiResponse = $this->load_balancer->deleteService(1234);

        $this->assertEquals('delete_service', $apiResponse->action->command);
        $this->assertEquals($this->load_balancer->id, $apiResponse->action->resources[0]->id);
        $this->assertEquals('load_balancer', $apiResponse->action->resources[0]->type);

        $this->assertLastRequestEquals('POST', '/load_balancers/4711/actions/delete_service');
        $this->assertLastRequestBodyParametersEqual(['listen_port' => 1234]);
    }

    /**
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function testDetachFromNetwork()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/loadBalancer_action_detach_from_network.json')));
        $apiResponse = $this->load_balancer->detachFromNetwork(1234);

        $this->assertEquals('detach_from_network', $apiResponse->action->command);
        $this->assertEquals('42', $apiResponse->action->resources[0]->id);
        $this->assertEquals('server', $apiResponse->action->resources[0]->type);
        $this->assertEquals('1234', $apiResponse->action->resources[1]->id);
        $this->assertEquals('network', $apiResponse->action->resources[1]->type);

        $this->assertLastRequestEquals('POST', '/load_balancers/4711/actions/detach_from_network');
        $this->assertLastRequestBodyParametersEqual(['network' => 1234]);
    }

    /**
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function testDisablePublicInterface()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/loadBalancer_action_disable_public_interface.json')));
        $apiResponse = $this->load_balancer->disablePublicInterface();

        $this->assertEquals('disable_public_interface', $apiResponse->action->command);
        $this->assertEquals('42', $apiResponse->action->resources[0]->id);
        $this->assertEquals('server', $apiResponse->action->resources[0]->type);

        $this->assertLastRequestEquals('POST', '/load_balancers/4711/actions/disable_public_interface');
    }

    /**
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function testEnablePublicInterface()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/loadBalancer_action_enable_public_interface.json')));
        $apiResponse = $this->load_balancer->enablePublicInterface();

        $this->assertEquals('enable_public_interface', $apiResponse->action->command);
        $this->assertEquals('42', $apiResponse->action->resources[0]->id);
        $this->assertEquals('server', $apiResponse->action->resources[0]->type);

        $this->assertLastRequestEquals('POST', '/load_balancers/4711/actions/enable_public_interface');
    }

    /**
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function testRemoveTarget()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__ . '/fixtures/loadBalancer_action_remove_target.json')));

        $loadBalancerTargetIp = new LoadBalancerTargetIp(
            '1.2.3.4',
        );

        $apiResponse = $this->load_balancer->removeTarget(
            'server',
            $loadBalancerTargetIp,
            ['selector' => 'env=prod']
        );

        $this->assertEquals('remove_target', $apiResponse->action->command);
        $this->assertEquals($this->load_balancer->id, $apiResponse->action->resources[0]->id);
        $this->assertEquals('load_balancer', $apiResponse->action->resources[0]->type);

        $this->assertLastRequestEquals('POST', '/load_balancers/4711/actions/remove_target');
        $this->assertLastRequestBodyParametersEqual([
            'type' => 'server',
            'ip' => ['ip' => '1.2.3.4'],
            'label_selector' => ['selector' => 'env=prod'],
        ]);
    }

    /**
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function testUpdateService()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__ . '/fixtures/loadBalancer_action_update_service.json')));

        $loadBalancerHealthCheckHttp = new LoadBalancerHealthCheckHttp(
            'example.com',
            '/',
            '{"status": "ok"}',
            [200, 300],
            false
        );

        $loadBalancerHealthCheck = new LoadBalancerHealthCheck(
            $loadBalancerHealthCheckHttp,
            15,
            4711,
            'http',
            3,
            10
        );

        $apiResponse = $this->load_balancer->updateService(
            80,
            $loadBalancerHealthCheck,
            443,
            'https',
            false
        );

        $this->assertEquals('update_service', $apiResponse->action->command);
        $this->assertEquals($this->load_balancer->id, $apiResponse->action->resources[0]->id);
        $this->assertEquals('load_balancer', $apiResponse->action->resources[0]->type);

        $this->assertLastRequestEquals('POST', '/load_balancers/4711/actions/update_service');
        $this->assertLastRequestBodyParametersEqual([
            'destination_port' => 80,
            'listen_port' => 443,
            'protocol' => 'https'
        ]);
    }
}
