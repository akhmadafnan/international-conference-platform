<?php

use App\Actions\Submission\CreateDraftSubmissionAction;
use App\Models\ConferenceEdition;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Database\DeadlockException;
use Illuminate\Database\QueryException;

require __DIR__.'/../../vendor/autoload.php';
$app = require __DIR__.'/../../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

if (! app()->environment('testing') || config('database.default') !== 'sqlite'
    || ! str_contains((string) config('database.connections.sqlite.database'), 'submission-race-')) {
    exit(2);
}
$actor = User::query()->findOrFail($argv[1]);
$registration = Registration::query()->findOrFail($argv[2]);
$edition = ConferenceEdition::query()->findOrFail($registration->membership->edition_id);
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
        app(CreateDraftSubmissionAction::class)->handle($actor, $edition, $registration, 'en');
        echo 'CREATED';
        exit(0);
    } catch (DomainException $exception) {
        if ($exception->getMessage() !== 'SUBMISSION_ALREADY_EXISTS') {
            throw $exception;
        }
        echo 'EXISTS';
        exit(0);
    } catch (QueryException|DeadlockException $exception) {
        if (! str_contains($exception->getMessage(), 'database is locked') || $attempt === 4) {
            throw $exception;
        }
        usleep(150000);
    }
}
exit(1);
