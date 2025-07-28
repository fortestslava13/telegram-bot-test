<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\Notifications\TelegramNotification;
use App\Services\TelegramService;
use Illuminate\Console\Command;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

class NotifyTasksCommand extends Command
{
    protected $signature = 'notify-tasks';

    protected $description = 'Sends a list of tasks to Telegram for each subscribed user';

    /**
     * @throws RequestException
     */
    public function handle(TelegramService $service)
    {
        $tasks = Http::get('https://jsonplaceholder.typicode.com/todos', ['completed' => 'false'])
            ->throw()
            ->collect()
            ->where('userId', '<=', 5)
            ->groupBy('userId');

        foreach ($tasks as $userId => $task) {
            if ($user = User::subscribed()->find($userId)) {
                foreach ($task->pluck('title') as $message) {
                    $user->notify(new TelegramNotification($message));
                }
            }
        }
    }
}
