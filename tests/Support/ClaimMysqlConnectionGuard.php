<?php

namespace Tests\Support;

use Dotenv\Dotenv;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Database\Connection;
use Illuminate\Database\DatabaseManager;
use RuntimeException;
use Throwable;

final class ClaimMysqlConnectionGuard
{
    private const PREFIX = 'CLAIM_MYSQL_ISOLATION: ';

    /** @var list<string> */
    public readonly array $applicationDatabases;

    /** @var list<string> */
    private readonly array $applicationUsers;

    private ?string $schema = null;

    private ?string $username = null;

    /** @param array<string, mixed> $applicationEnvironment */
    public function __construct(private readonly DatabaseManager $db, private readonly Repository $config, array $applicationEnvironment)
    {
        $url = $config->get('database.connections.mysql.url');
        $fileUrl = $applicationEnvironment['DB_URL'] ?? null;
        $databases = ['international_conference_platform', $config->get('database.connections.mysql.database'),
            $config->get('database.connections.mysql.read.database'), $config->get('database.connections.mysql.write.database'),
            $applicationEnvironment['DB_DATABASE'] ?? null];
        $users = [$config->get('database.connections.mysql.username'), $applicationEnvironment['DB_USERNAME'] ?? null];
        foreach ([$url, $fileUrl] as $value) {
            if (is_string($value) && $value !== '') {
                $parts = parse_url($value);
                if ($parts === false) {
                    throw new RuntimeException(self::PREFIX.'Cannot capture application identity.');
                }
                $databases[] = rawurldecode(ltrim($parts['path'] ?? '', '/'));
                $users[] = rawurldecode($parts['user'] ?? '');
            }
        }
        $this->applicationDatabases = array_values(array_unique(array_filter($databases, fn ($value) => is_string($value) && $value !== '')));
        $this->applicationUsers = array_values(array_unique(array_filter($users, fn ($value) => is_string($value) && $value !== '')));
    }

    public static function capture(DatabaseManager $db, Repository $config, string $root): self
    {
        try {
            return new self($db, $config, Dotenv::createArrayBacked($root)->safeLoad());
        } catch (Throwable) {
            throw new RuntimeException(self::PREFIX.'Cannot capture application identity.');
        }
    }

    /** @return array<string, string> */
    public static function environment(): array
    {
        $values = [];
        foreach (['ENABLED', 'DATABASE', 'HOST', 'PORT', 'USERNAME', 'PASSWORD'] as $key) {
            $value = getenv('ICHES_CLAIM_MYSQL_TEST_'.$key);
            if ($value !== false) {
                $values[$key] = $value;
            }
        }

        return $values;
    }

    /** @param array<string, string> $settings */
    public function connect(array $settings, ?string $requestedSchema = null): Connection
    {
        try {
            foreach (['ENABLED', 'DATABASE', 'HOST', 'PORT', 'USERNAME', 'PASSWORD'] as $key) {
                if (! isset($settings[$key]) || $settings[$key] === '') {
                    throw new RuntimeException(self::PREFIX.'Explicit opt-in and test-only connection fields required.');
                }
            }
            if ($settings['ENABLED'] !== '1' || $this->config->get('app.env') !== 'testing'
                || ! preg_match('/^iches_claim_test_[a-z0-9_]+$/D', $settings['DATABASE'])
                || strlen($settings['DATABASE']) > 64
                || in_array($settings['DATABASE'], $this->applicationDatabases, true)
                || ($requestedSchema !== null && $requestedSchema !== $settings['DATABASE'])
                || ! preg_match('/^[a-zA-Z0-9_]+$/D', $settings['USERNAME'])
                || in_array($settings['USERNAME'], $this->applicationUsers, true)
                || ! ctype_digit($settings['PORT']) || (int) $settings['PORT'] < 1 || (int) $settings['PORT'] > 65535) {
                throw new RuntimeException(self::PREFIX.'Opt-in, target or test-only account rejected.');
            }
            $this->schema = $settings['DATABASE'];
            $this->username = $settings['USERNAME'];
            foreach (array_unique(['mysql', ...array_keys($this->db->getConnections())]) as $name) {
                $this->db->purge($name);
            }
            // Rebuild instead of merging: inherited URLs, sockets, replicas and credentials cannot survive.
            $this->config->set('database.connections', ['mysql' => [
                'driver' => 'mysql', 'url' => null, 'host' => $settings['HOST'], 'port' => $settings['PORT'],
                'database' => $this->schema, 'username' => $this->username, 'password' => $settings['PASSWORD'],
                'unix_socket' => '', 'charset' => 'utf8mb4', 'collation' => 'utf8mb4_0900_ai_ci',
                'prefix' => '', 'strict' => true, 'options' => [],
            ]]);
            $this->config->set('database.default', 'mysql');
            $this->config->set('activitylog.database_connection', 'mysql');
            $this->config->set('cache.default', 'array');
            $this->config->set('session.driver', 'array');
            $this->config->set('queue.default', 'sync');
            $this->db->setDefaultConnection('mysql');
            $this->db->reconnect('mysql');

            return $this->assertVerified();
        } catch (Throwable $error) {
            $this->abort($error);
        }
    }

    public function assertVerified(): Connection
    {
        try {
            if ($this->schema === null || $this->config->get('database.default') !== 'mysql') {
                throw new RuntimeException(self::PREFIX.'Connection has not been authorized.');
            }
            $connection = $this->db->connection('mysql');
            $identity = $connection->selectOne('SELECT DATABASE() AS database_name, CURRENT_USER() AS authenticated_user, CURRENT_ROLE() AS active_roles, @@GLOBAL.mandatory_roles AS mandatory_roles, @@GLOBAL.partial_revokes AS partial_revokes', [], false);
            if ($identity === null || $identity->database_name !== $this->schema
                || in_array($identity->database_name, $this->applicationDatabases, true)
                || explode('@', $identity->authenticated_user, 2)[0] !== $this->username
                || $identity->active_roles !== 'NONE' || $identity->mandatory_roles !== '') {
                throw new RuntimeException(self::PREFIX.'Actual schema or authenticated account mismatch.');
            }
            $scope = '`'.((bool) $identity->partial_revokes ? $this->schema : addcslashes($this->schema, '_%\\')).'`.*';
            $schemaGrant = false;
            foreach ($connection->select('SHOW GRANTS FOR CURRENT_USER()', [], false) as $row) {
                $grant = array_values((array) $row)[0] ?? '';
                if (! is_string($grant) || str_contains($grant, 'WITH GRANT OPTION')
                    || ! preg_match('/^GRANT (.+) ON (.+) TO /D', $grant, $matches)) {
                    throw new RuntimeException(self::PREFIX.'Roles, delegated or uninspectable privileges rejected.');
                }
                if ($matches[1] === 'USAGE' && $matches[2] === '*.*') {
                    continue;
                }
                if ($matches[2] !== $scope) {
                    throw new RuntimeException(self::PREFIX.'Privileges outside the exact temporary schema rejected.');
                }
                $schemaGrant = true;
            }
            if (! $schemaGrant) {
                throw new RuntimeException(self::PREFIX.'Explicit temporary-schema grant required.');
            }

            return $connection;
        } catch (Throwable $error) {
            $this->abort($error);
        }
    }

    private function abort(Throwable $error): never
    {
        try {
            $this->db->purge('mysql');
        } catch (Throwable) {
            // Cleanup failure must not expose connector diagnostics or credentials.
        }
        $this->config->set('database.connections', []);
        $this->config->set('database.default', 'claim_mysql_blocked');
        $message = str_starts_with($error->getMessage(), self::PREFIX) ? $error->getMessage() : self::PREFIX.'Connection identity or privileges could not be verified.';

        throw new RuntimeException($message);
    }
}
