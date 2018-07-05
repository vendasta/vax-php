<?php

namespace Vendasta\Vax;

use Google\Protobuf\Internal\Message;
use Grpc\Channel;
use Grpc\CallCredentials;
use Grpc\ChannelCredentials;
use Vendasta\Vax\Auth\FetchAuthTokenCache;
use Vendasta\Vax\Auth\FetchVendastaAuthToken;


/**
 * Class GRPCClient
 * @package Vendasta\Accounts\V1\Vax
 *
 * Base GRPCClient class which adds authorization to all outbound grpc requests
 */
class GRPCClient extends VAXClient
{
    private $auth;
    private $secure;

    /**
     * JSONClient constructor.
     * @param string $host
     * @param string $scope
     * @param bool $secure
     * @param float $default_timeout
     */
    public function __construct(string $host, string $scope, bool $secure = true, float $default_timeout = 10000)
    {
        parent::__construct($default_timeout);
        $this->auth = new FetchAuthTokenCache(new FetchVendastaAuthToken($scope));
        $this->secure = $secure;
    }

    protected function getClientOptions(): array {
        return [
            'credentials' => ($this->secure ? ChannelCredentials::createSsl() : ChannelCredentials::createInsecure()),
        ];
    }

    private function buildGRPCOptions(array $options = []): array
    {
        $opts = $this->buildVAXOptions($options);
        $grpcOpts = [
            'timeout' => $opts->timeout * 1000 // microseconds,
        ];

        if ($opts->include_token) {
            $auth = $this->auth;
            $grpcOpts['call_credentials_callback'] = function() use ($auth) {
                return ['authorization' => ['Bearer ' . $auth->fetchToken()]];
            };
        }
        return $grpcOpts;
    }

    /**
     * @param callable $client_call
     * @param Message $req
     * @param array $options possible keys:
     *              \Vendasta\Vax\RequestOptions::*
     * @throws SDKException on failed call
     * @return Message
     */
    protected function doRequest(callable $client_call, Message $req, array $options = [])
    {
        list($response, $status) = $client_call($req, [], $this->buildGRPCOptions($options))->wait();
        if ($status->code) {
            if ($status->code == 16) {
                $this->auth->invalidateToken();
                list($response, $status) = $client_call($req, [], $this->buildGRPCOptions($options))->wait();
                if ($status->code) {
                    throw new SDKException($status->details, $status->code);
                }
            } else {
                throw new SDKException($status->details, $status->code);
            }
        }
        return $response;
    }
}
