<?php

use App\Actions\Academic\AcademicWorkflow;
use App\Models\Submission;
use App\Models\User;
use App\Support\Authorization\ActiveConferenceEditionContext;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Database\QueryException;

// Separate-process worker. Never reads a production connection: both the
// environment and runtime configuration must select this disposable SQLite file.
require __DIR__.'/../../vendor/autoload.php';
$app = require __DIR__.'/../../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

if (app()->environment() !== 'testing' || config('database.default') !== 'sqlite'
    || ! str_contains((string) config('database.connections.sqlite.database'), 'academic-race-')) {
    fwrite(STDERR, 'Isolated SQLite required');
    exit(2);
}
config(['filesystems.disks.private.root' => $argv[4]]);

$actor = User::query()->findOrFail($argv[1]);
$submission = Submission::query()->findOrFail($argv[2]);
app(ActiveConferenceEditionContext::class)->activate($submission->edition_id);
setPermissionsTeamId($submission->edition_id);
// Both workers announce readiness before the parent releases the barrier.
echo "READY\n";
fflush(STDOUT);
$deadline = microtime(true) + 20;
while (! file_exists($argv[3])) {
    if (microtime(true) > $deadline) {
        exit(3);
    }
    usleep(10000);
}

for ($attempt = 0; $attempt < 5; $attempt++) {
    try {
        $decision = app(AcademicWorkflow::class)->decide($actor, $submission, [
            'version' => 1, 'outcome' => 'ACCEPTED',
            'author_message' => 'Concurrent acceptance', 'rationale' => 'Independent human decision',
        ]);
        echo json_encode(['decision' => $decision->id], JSON_THROW_ON_ERROR);
        exit(0);
    } catch (QueryException $exception) {
        if (! str_contains($exception->getMessage(), 'database is locked') || $attempt === 4) {
            throw $exception;
        }
        usleep(150000);
    }
}
exit(1);
