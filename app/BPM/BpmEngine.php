<?php
namespace App\Services\BPM;

use App\Models\Flow;
use App\Models\Ticket;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

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
     */
    public function advance(Ticket $ticket, array $input): Ticket
    {
        $current    = $ticket->currentActivity;
        $mergedData = array_merge($ticket->data ?? [], $input);
        $next       = null;

        foreach ($current->outgoing as $transition) {
            $cond = $transition->condition;
            if (empty($cond)) {
                // transición sin condición → ruta por defecto
                $next = $transition->to;
                break;
            }
            try {
                if ($this->expr->evaluate($cond, $mergedData)) {
                    $next = $transition->to;
                    break;
                }
            } catch (\Throwable $e) {
                // en caso de error de sintaxis o evaluación, ignorar
            }
        }

        if (! $next) {
            throw new \Exception("No hay transiciones válidas desde la actividad actual.");
        }

        // actualizar Ticket
        $ticket->update([
            'current_activity_id' => $next->id,
            'status'              => $next->type === 'endEvent' ? 'completed' : 'open',
            'data'                => $mergedData,
        ]);

        return $ticket;
    }
}
