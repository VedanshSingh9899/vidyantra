<?php











namespace Predis\Connection\Resource;

use InvalidArgumentException;
use Predis\Connection\ParametersInterface;
use Predis\Connection\Resource\Exception\StreamInitException;
use Psr\Http\Message\StreamInterface;

class StreamFactory implements StreamFactoryInterface
{
    



    public function createStream(ParametersInterface $parameters): StreamInterface
    {
        $parameters = $this->assertParameters($parameters);

        switch ($parameters->scheme) {
            case 'tcp':
            case 'redis':
                $stream = $this->tcpStreamInitializer($parameters);
                break;

            case 'unix':
                $stream = $this->unixStreamInitializer($parameters);
                break;

            case 'tls':
            case 'rediss':
                $stream = $this->tlsStreamInitializer($parameters);
                break;

            default:
                throw new InvalidArgumentException("Invalid scheme: '{$parameters->scheme}'.");
        }

        return new Stream($stream);
    }

    







    protected function assertParameters(ParametersInterface $parameters): ParametersInterface
    {
        switch ($parameters->scheme) {
            case 'tcp':
            case 'redis':
            case 'unix':
            case 'tls':
            case 'rediss':
                break;

            default:
                throw new InvalidArgumentException("Invalid scheme: '$parameters->scheme'.");
        }

        return $parameters;
    }

    







    protected function tcpStreamInitializer(ParametersInterface $parameters)
    {
        if (!filter_var($parameters->host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            $address = "tcp://$parameters->host:$parameters->port";
        } else {
            $address = "tcp://[$parameters->host]:$parameters->port";
        }

        $flags = STREAM_CLIENT_CONNECT;

        if (isset($parameters->async_connect) && $parameters->async_connect) {
            $flags |= STREAM_CLIENT_ASYNC_CONNECT;
        }

        if (isset($parameters->persistent)) {
            if (false !== $persistent = filter_var($parameters->persistent, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)) {
                $flags |= STREAM_CLIENT_PERSISTENT;

                if ($persistent === null) {
                    $address = "{$address}/{$parameters->persistent}";
                }
            }
        }

        return $this->createStreamSocket($parameters, $address, $flags);
    }

    







    protected function unixStreamInitializer(ParametersInterface $parameters)
    {
        if (!isset($parameters->path)) {
            throw new InvalidArgumentException('Missing UNIX domain socket path.');
        }

        $flags = STREAM_CLIENT_CONNECT;

        if (isset($parameters->persistent)) {
            if (false !== $persistent = filter_var($parameters->persistent, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)) {
                $flags |= STREAM_CLIENT_PERSISTENT;

                if ($persistent === null) {
                    throw new InvalidArgumentException(
                        'Persistent connection IDs are not supported when using UNIX domain sockets.'
                    );
                }
            }
        }

        return $this->createStreamSocket($parameters, "unix://{$parameters->path}", $flags);
    }

    







    protected function tlsStreamInitializer(ParametersInterface $parameters)
    {
        $resource = $this->tcpStreamInitializer($parameters);
        $metadata = stream_get_meta_data($resource);

        
        if (isset($metadata['crypto'])) {
            return $resource;
        }

        if (isset($parameters->ssl) && is_array($parameters->ssl)) {
            $options = $parameters->ssl;
        } else {
            $options = [];
        }

        if (!isset($options['crypto_type'])) {
            $options['crypto_type'] = STREAM_CRYPTO_METHOD_TLS_CLIENT;
        }

        $context_options = function_exists('stream_context_set_options')
            ? stream_context_set_options($resource, ['ssl' => $options])
            : stream_context_set_option($resource, ['ssl' => $options]);

        if (!$context_options) {
            $this->onInitializationError($resource, $parameters, 'Error while setting SSL context options');
        }

        if (!stream_socket_enable_crypto($resource, true, $options['crypto_type'])) {
            $this->onInitializationError($resource, $parameters, 'Error while switching to encrypted communication');
        }

        return $resource;
    }

    









    protected function createStreamSocket(ParametersInterface $parameters, $address, $flags)
    {
        $timeout = (isset($parameters->timeout) ? (float) $parameters->timeout : 5.0);
        $context = stream_context_create(['socket' => ['tcp_nodelay' => (bool) $parameters->tcp_nodelay]]);

        if (
            (isset($parameters->persistent) && $parameters->persistent)
            && (isset($parameters->conn_uid) && $parameters->conn_uid)
        ) {
            $conn_uid = '/' . $parameters->conn_uid;
        } else {
            $conn_uid = '';
        }

        
        $address = $address . $conn_uid;

        if (!$resource = @stream_socket_client($address, $errno, $errstr, $timeout, $flags, $context)) {
            $this->onInitializationError($resource, $parameters, trim($errstr), $errno);
        }

        if (isset($parameters->read_write_timeout)) {
            $rwtimeout = (float) $parameters->read_write_timeout;
            $rwtimeout = $rwtimeout > 0 ? $rwtimeout : -1;
            $timeoutSeconds = floor($rwtimeout);
            $timeoutUSeconds = ($rwtimeout - $timeoutSeconds) * 1000000;
            stream_set_timeout($resource, $timeoutSeconds, $timeoutUSeconds);
        }

        return $resource;
    }

    






    protected function onInitializationError($stream, ParametersInterface $parameters, string $message, int $code = 0): void
    {
        if (is_resource($stream)) {
            fclose($stream);
        }

        throw new StreamInitException("$message [{$parameters}]", $code);
    }
}
