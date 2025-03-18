<?php

declare(strict_types=1);

namespace Cndrsdrmn\LaravelPasswords;

use Illuminate\Auth\Passwords\PasswordBrokerManager;
use Illuminate\Auth\Passwords\TokenRepositoryInterface;
use Illuminate\Contracts\Auth\PasswordBroker;
use InvalidArgumentException;

final class BrokerManager extends PasswordBrokerManager
{
    /**
     * Create a token repository instance based on the given configuration.
     */
    protected function createTokenRepository(array $config): TokenRepositoryInterface
    {
        if (isset($config['driver']) && $config['driver'] === 'cache') {
            return parent::createTokenRepository($config);
        }

        $key = $this->app['config']['app.key'];

        if (str_starts_with((string) $key, 'base64:')) {
            $key = base64_decode(mb_substr((string) $key, 7));
        }

        return new TokenRepository(
            $this->app['db']->connection($config['connection'] ?? null),
            $this->app['hash'],
            $config['table'],
            $key,
            ($config['expire'] ?? 60) * 60,
            $config['throttle'] ?? 0,
        );
    }

    /**
     * Resolve the given broker.
     *
     * @param  string  $name
     *
     * @throws InvalidArgumentException
     */
    protected function resolve($name): PasswordBroker
    {
        $config = $this->getConfig($name);

        if (is_null($config)) {
            throw new InvalidArgumentException("Password resetter [{$name}] is not defined.");
        }

        return new Broker(
            $this->createTokenRepository($config),
            $this->app['auth']->createUserProvider($config['provider'] ?? null),
            $this->app['events'] ?? null,
        );
    }
}
