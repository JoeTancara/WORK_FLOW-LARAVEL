<?php
namespace App\Services\BPM;

use App\Models\Flow;
use App\Models\Ticket;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Illuminate\Support\Facades\Log;

class BpmEngine
{
    protected ExpressionLanguage $expr;

    public function __construct()
    {
        $this->expr = new ExpressionLanguage();
    }

    /**
     * Inicia un Ticket en la actividad de inicio del Flow.
     */
    public function start(int $flowId, int $userId): Ticket
    {
        $flow  = Flow::findOrFail($flowId);
        $start = $flow->activities()
                      ->where('type', 'startEvent')
                      ->firstOrFail();

        return Ticket::create([
            'user_id'             => $userId,
            'flow_id'             => $flowId,
            'current_activity_id' => $start->id,
            'data'                => [],
        ]);
    }

    /**
     * Avanza un Ticket a la siguiente actividad evaluando condiciones.
     * Si approved === false (En proceso), simplemente actualiza datos y deja el ticket abierto.
     */
    public function advance(Ticket $ticket, array $input): Ticket
    {
        $current    = $ticket->currentActivity;
        $mergedData = array_merge($ticket->data ?? [], $input);

        // 1. Si llega approved y su valor es falsy ("0", 0, false, null, "")
        if (array_key_exists('approved', $mergedData) && ! $mergedData['approved']) {
            $ticket->update([
                'data'   => $mergedData,
                'status' => 'open',
            ]);
            return $ticket;
        }

        // 2. approved truthy → buscar transición condicionada
        $next = null;
        foreach ($current->outgoing as $t) {
            $cond = trim($t->condition);
            if ($cond !== '' && $this->expr->evaluate($cond, $mergedData)) {
                $next = $t->to;
                break;
            }
        }

        // 3. Si no hubo, buscar sin condición
        if (! $next) {
            $default = $current->outgoing->first(fn($t) => trim($t->condition) === '');
            if ($default) {
                $next = $default->to;
            }
        }

        // 4. Fallback a primera transición
        if (! $next) {
            $fallback = $current->outgoing->first();
            if ($fallback) {
                $next = $fallback->to;
                Log::warning("BPM: Ticket {$ticket->id} usando fallback desde {$current->name}");
            }
        }

        // 5. Si sigue sin next, completamos el ticket
        if (! $next) {
            $ticket->update(['status' => 'completed']);
            return $ticket->refresh();
        }

        // 6. Avanzamos al siguiente nodo
        $ticket->update([
            'current_activity_id' => $next->id,
            'status'              => $next->type === 'endEvent' ? 'completed' : 'open',
            'data'                => $mergedData,
        ]);

        return $ticket->refresh();
    }
}
