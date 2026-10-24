<?php

use App\Actions\Academic\ClaimAbstractReviewAction;
use App\Models\Submission;
use App\Models\User;
use App\Support\Authorization\ActiveConferenceEditionContext;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Database\DatabaseServiceProvider;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Bootstrap\RegisterProviders;
use Illuminate\Validation\ValidationException;
use Tests\Support\ClaimMysqlConnectionGuard;

require dirname(__DIR__, 2).'/vendor/autoload.php';

try {
    if ($argc !== 7) {
        throw new RuntimeException('CLAIM_MYSQL_ISOLATION: Worker arguments rejected.');
    }
    [$script, $schema, $editionId, $paperId, $reviewerId, $snapshotId, $barrier] = $argv;
    $app = require dirname(__DIR__, 2).'/bootstrap/app.php';
    $guard = null;
    // Isolate the database before application providers can resolve any connection.
    $app->beforeBootstrapping(RegisterProviders::class, function (Application $app) use (&$guard, $schema): void {
        $app->register(DatabaseServiceProvider::class);
        $guard = ClaimMysqlConnectionGuard::capture($app->make('db'), $app->make('config'), $app->basePath());
        $guard->connect(ClaimMysqlConnectionGuard::environment(), $schema);
    });
    $app->make(Kernel::class)->bootstrap();
    if (! $guard instanceof ClaimMysqlConnectionGuard) {
        throw new RuntimeException('CLAIM_MYSQL_ISOLATION: Worker guard was not initialized.');
    }
    $guard->assertVerified();
    app(ActiveConferenceEditionContext::class)->activate($editionId);
    setPermissionsTeamId($editionId);
    echo "READY\n";
    flush();
    $deadline = microtime(true) + 20;
    while (file_get_contents($barrier) !== 'go') {
        if (microtime(true) > $deadline) {
            throw new RuntimeException('Worker start barrier timed out.');
        }
        usleep(10000);
    }
    $guard->assertVerified();
    try {
        $assignment = app(ClaimAbstractReviewAction::class)->handle(User::query()->findOrFail($reviewerId), Submission::query()->findOrFail($paperId), [
            'version' => 1, 'snapshot_id' => $snapshotId, 'no_conflict_declared' => true,
        ]);
        echo 'CLAIMED:'.$assignment->id."\n";
    } catch (ValidationException) {
        echo "REJECTED\n";
    }
} catch (Throwable $error) {
    $diagnostic = str_starts_with($error->getMessage(), 'CLAIM_MYSQL_ISOLATION: ')
        ? $error->getMessage() : 'CLAIM_MYSQL_ISOLATION: Worker stopped; guarded operation failed.';
    fwrite(STDERR, $diagnostic.PHP_EOL);
    exit(1);
}
