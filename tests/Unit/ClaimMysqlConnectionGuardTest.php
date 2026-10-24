<?php

use Illuminate\Config\Repository;
use Illuminate\Database\Connection;
use Illuminate\Database\DatabaseManager;
use Symfony\Component\Process\Process;
use Tests\Support\ClaimMysqlConnectionGuard;

beforeEach(function () {
    $this->settings = ['ENABLED' => '1', 'DATABASE' => 'iches_claim_test_safety', 'HOST' => '127.0.0.1', 'PORT' => '3306', 'USERNAME' => 'claim_worker', 'PASSWORD' => 'test-only-secret'];
    $this->config = new Repository([
        'app' => ['env' => 'testing'],
        'database' => ['default' => 'mysql', 'connections' => ['mysql' => [
            'database' => ':memory:', 'username' => 'app_account', 'password' => 'application-secret',
            'url' => 'mysql://url_account:url-secret@localhost/actual_app_schema',
            'read' => ['database' => 'read_app_schema'], 'write' => ['database' => 'write_app_schema'],
        ]]],
    ]);
    $this->stale = Mockery::mock(Connection::class);
    $this->stale->shouldNotReceive('selectOne');
    $this->connection = Mockery::mock(Connection::class);
    foreach (['statement', 'insert', 'update', 'delete'] as $method) {
        $this->connection->shouldNotReceive($method);
    }
    $this->db = Mockery::mock(DatabaseManager::class);
    $this->events = [];
    $this->db->shouldReceive('getConnections')->andReturn(['mysql' => $this->stale]);
    $this->db->shouldReceive('purge')->with('mysql')->andReturnUsing(function () {
        $this->events[] = 'purge';
    });
    $this->db->shouldReceive('setDefaultConnection')->with('mysql')->andReturnNull();
    $this->db->shouldReceive('reconnect')->with('mysql')->andReturnUsing(function () {
        $this->events[] = 'reconnect';

        return $this->connection;
    });
    $this->db->shouldReceive('connection')->with('mysql')->andReturn($this->connection);
    $this->guard = new ClaimMysqlConnectionGuard($this->db, $this->config, ['DB_DATABASE' => 'file_app_schema', 'DB_USERNAME' => 'file_app_account']);
    $this->identity = (object) ['database_name' => 'iches_claim_test_safety', 'authenticated_user' => 'claim_worker@localhost', 'partial_revokes' => 0, 'active_roles' => 'NONE', 'mandatory_roles' => ''];
    $this->grants = [(object) ['grant' => 'GRANT USAGE ON *.* TO `claim_worker`@`localhost`'], (object) ['grant' => 'GRANT ALL PRIVILEGES ON `iches\_claim\_test\_safety`.* TO `claim_worker`@`localhost`']];
});

afterEach(function () {
    Mockery::close();
});

function mockClaimIdentity(object $test): void
{
    $test->connection->shouldReceive('selectOne')->with(Mockery::on(fn ($sql) => str_contains($sql, 'SELECT DATABASE()')), [], false)->andReturn($test->identity);
    $test->connection->shouldReceive('select')->with('SHOW GRANTS FOR CURRENT_USER()', [], false)->andReturn($test->grants);
}

test('cached application connection is purged and replaced using only explicit test configuration', function () {
    mockClaimIdentity($this);
    expect($this->guard->connect($this->settings))->toBe($this->connection)
        ->and($this->events)->toBe(['purge', 'reconnect'])
        ->and($this->guard->applicationDatabases)->toContain('international_conference_platform', 'file_app_schema', 'actual_app_schema')
        ->and($this->config->get('database.connections.mysql.password'))->toBe('test-only-secret')
        ->and($this->config->get('database.connections.mysql.url'))->toBeNull()
        ->and($this->config->has('database.connections.mysql.read'))->toBeFalse()
        ->and($this->config->has('database.connections.mysql.write'))->toBeFalse();
    expect($this->guard->assertVerified())->toBe($this->connection);
});

test('missing invalid opt-in or application credentials are rejected before any connection attempt', function (string $case) {
    if ($case === 'missing opt-in') {
        unset($this->settings['ENABLED']);
    } elseif ($case === 'invalid opt-in') {
        $this->settings['ENABLED'] = 'true';
    } elseif ($case === 'missing password') {
        unset($this->settings['PASSWORD']);
    } elseif ($case === 'missing username') {
        unset($this->settings['USERNAME']);
    } elseif ($case === 'missing host') {
        unset($this->settings['HOST']);
    } elseif ($case === 'application account') {
        $this->settings['USERNAME'] = 'app_account';
    } elseif ($case === 'file application account') {
        $this->settings['USERNAME'] = 'file_app_account';
    } elseif ($case === 'URL application account') {
        $this->settings['USERNAME'] = 'url_account';
    } elseif ($case === 'application database') {
        $this->settings['DATABASE'] = 'international_conference_platform';
    } elseif ($case === 'invalid schema') {
        $this->settings['DATABASE'] = 'arbitrary_schema';
    } elseif ($case === 'invalid port') {
        $this->settings['PORT'] = '0';
    } else {
        $this->config->set('app.env', 'local');
    }
    $this->connection->shouldNotReceive('selectOne');
    expect(fn () => $this->guard->connect($this->settings))->toThrow(RuntimeException::class, 'CLAIM_MYSQL_ISOLATION:');
    expect($this->events)->not->toContain('reconnect');
    expect($this->config->get('database.connections'))->toBe([]);
})->with(['missing opt-in', 'invalid opt-in', 'missing password', 'missing username', 'missing host', 'application account', 'file application account', 'URL application account', 'application database', 'invalid schema', 'invalid port', 'wrong environment']);

test('actual identity mismatch blocks parent migrations and worker READY', function (string $case) {
    if ($case === 'wrong schema') {
        $this->identity->database_name = 'international_conference_platform';
    } elseif ($case === 'wrong credentials') {
        $this->identity->authenticated_user = 'app_account@localhost';
    } elseif ($case === 'active role') {
        $this->identity->active_roles = '`dangerous_role`@`%`';
    } else {
        $this->identity->mandatory_roles = '`mandatory_role`@`%`';
    }
    mockClaimIdentity($this);
    $migrationRan = false;
    $ready = false;
    try {
        $this->guard->connect($this->settings);
        $migrationRan = true;
        $ready = true;
    } catch (RuntimeException $error) {
        expect($error->getMessage())->toContain('mismatch')->not->toContain('secret');
    }
    expect($migrationRan)->toBeFalse()->and($ready)->toBeFalse()
        ->and($this->config->get('database.default'))->toBe('claim_mysql_blocked');
})->with(['wrong schema', 'wrong credentials', 'active role', 'mandatory role']);

test('global inherited delegated or other-schema privileges fail closed', function (string $grant) {
    $this->grants[] = (object) ['grant' => $grant];
    mockClaimIdentity($this);
    expect(fn () => $this->guard->connect($this->settings))->toThrow(RuntimeException::class);
    expect($this->config->get('database.connections'))->toBe([]);
})->with([
    'GRANT SELECT ON *.* TO `claim_worker`@`localhost`',
    'GRANT ALL PRIVILEGES ON `international_conference_platform`.* TO `claim_worker`@`localhost`',
    'GRANT `admin_role`@`%` TO `claim_worker`@`localhost`',
    'GRANT ALL PRIVILEGES ON `iches\_claim\_test\_safety`.* TO `claim_worker`@`localhost` WITH GRANT OPTION',
    'GRANT ALL PRIVILEGES ON `iches_claim_test_safety`.* TO `claim_worker`@`localhost`',
]);

test('literal schema grants are accepted only when partial revokes removes wildcard semantics', function () {
    $this->identity->partial_revokes = 1;
    $this->grants[1]->grant = 'GRANT ALL PRIVILEGES ON `iches_claim_test_safety`.* TO `claim_worker`@`localhost`';
    mockClaimIdentity($this);
    expect($this->guard->connect($this->settings))->toBe($this->connection);
});

test('a test-prefixed application identity is still forbidden and remains captured after overrides', function () {
    $guard = new ClaimMysqlConnectionGuard($this->db, $this->config, ['DB_DATABASE' => $this->settings['DATABASE']]);
    expect(fn () => $guard->connect($this->settings))->toThrow(RuntimeException::class)
        ->and($guard->applicationDatabases)->toContain($this->settings['DATABASE']);
    expect($this->events)->not->toContain('reconnect');
});

test('loss of identity before READY is rejected even after initial verification succeeded', function () {
    mockClaimIdentity($this);
    $this->guard->connect($this->settings);
    $this->identity->database_name = 'international_conference_platform';
    expect(fn () => $this->guard->assertVerified())->toThrow(RuntimeException::class, 'mismatch');
    expect($this->config->get('database.connections'))->toBe([]);
});

test('accounts without an inspectable temporary-schema grant cannot proceed', function () {
    $this->grants = [(object) ['grant' => 'GRANT USAGE ON *.* TO `claim_worker`@`localhost`']];
    mockClaimIdentity($this);
    expect(fn () => $this->guard->connect($this->settings))->toThrow(RuntimeException::class, 'temporary-schema grant required');
});

test('connector and privilege errors produce sanitized diagnostics without secrets or URLs', function () {
    $this->connection->shouldReceive('selectOne')->andThrow(new RuntimeException('mysql://app:application-secret@localhost password=test-only-secret'));
    try {
        $this->guard->connect($this->settings);
        test()->fail('Expected isolation failure');
    } catch (RuntimeException $error) {
        expect($error->getMessage())->toBe('CLAIM_MYSQL_ISOLATION: Connection identity or privileges could not be verified.')
            ->and($error->getPrevious())->toBeNull();
    }
});

test('worker rejects absent opt-in or mismatched authorized schema before READY without contacting MySQL', function (bool $mismatch) {
    $environment = ['APP_ENV' => 'testing', 'APP_CONFIG_CACHE' => getcwd().'/bootstrap/cache/claim-guard-nonexistent.php'];
    foreach ($this->settings as $key => $value) {
        $environment['ICHES_CLAIM_MYSQL_TEST_'.$key] = $value;
    }
    $environment['ICHES_CLAIM_MYSQL_TEST_PORT'] = '1';
    if (! $mismatch) {
        $environment['ICHES_CLAIM_MYSQL_TEST_ENABLED'] = false;
    }
    $schema = $mismatch ? 'iches_claim_test_different' : 'iches_claim_test_safety';
    $worker = new Process([PHP_BINARY, getcwd().'/tests/Support/abstract-claim-worker.php', $schema, 'edition', 'paper', 'reviewer', 'snapshot', 'unused-barrier'], getcwd(), $environment);
    $worker->setTimeout(15);
    expect($worker->run())->toBe(1)->and($worker->getOutput())->not->toContain('READY')
        ->and($worker->getErrorOutput())->toContain('CLAIM_MYSQL_ISOLATION:')->not->toContain('secret', 'mysql://');
})->with([false, true]);
