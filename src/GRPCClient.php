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

    protected function getClientOptions(): array
    {
        return [
            'credentials' => ($this->secure ? ChannelCredentials::createSsl() : ChannelCredentials::createInsecure()),
        ];
    }

    private function buildGRPCOptions(Options $opts): array
    {
        $grpcOpts = [
            'timeout' => $opts->timeout * 1000 // microseconds,
        ];

        if ($opts->include_token) {
            $auth = $this->auth;
            $grpcOpts['call_credentials_callback'] = function () use ($auth) {
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
        $opts = $this->buildVAXOptions($options);
        $max_time = $this->getMaxCallDuration($opts);

        while (1) {
            try {
                return $this->call($client_call, $req, $opts);
            } catch (SDKException $e) {
                if ($opts->retry_options != null) {
                    if (!$opts->retry_options->shouldRetry($e->getCode())) {
                        throw $e;
                    }

                    $time = $opts->retry_options->pause();
                    if ($this->isRetryWithinMaxCallDuration($time, $max_time)) {
                        // Convert milliseconds to microseconds
                        usleep($time * 1000);
                    } else {
                        throw $e;
                    }
                } else {
                    throw $e;
                }
            }
        }
    }

    /**
     * @param callable $client_call
     * @param Message $req
     * @param Options $opts
     * @return Message
     * @throws SDKException
     */
    protected function call(callable $client_call, Message $req, Options $opts)
    {
        list($response, $status) = $client_call($req, [], $this->buildGRPCOptions($opts))->wait();
        if ($status->code) {
            throw new SDKException($status->details, GRPCCodes::ToHTTPStatusCode($status->code));
        }
        return $response;
    }
}
