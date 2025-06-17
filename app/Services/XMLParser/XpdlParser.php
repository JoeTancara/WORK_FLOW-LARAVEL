<?php
namespace App\Services\XMLParser;

use App\Models\Process;
use App\Models\Flow;
use App\Models\Activity;
use App\Models\Transition;

class XpdlParser
{
    /**
     * Parsea un .xpdl y guarda Process, Flow, Activity y Transition.
     *
     * @param  string  $filePath
     * @return \App\Models\Flow
     *
     * @throws \Exception
     */
    public function parse(string $filePath): Flow
    {
        // 0) Asegurar existencia y permisos
        if (! file_exists($filePath) || ! is_readable($filePath)) {
            throw new \Exception("Archivo XPDL no encontrado o no legible: {$filePath}");
        }
        $xmlContent = file_get_contents($filePath);
        if ($xmlContent === false) {
            throw new \Exception("Error al leer el archivo XPDL: {$filePath}");
        }

        // 1) Carga segura del XML
        libxml_use_internal_errors(true);
        libxml_clear_errors();
        $xml = simplexml_load_string($xmlContent, 'SimpleXMLElement', LIBXML_NOCDATA);
        $errs = libxml_get_errors();
        if ($xml === false || count($errs) > 0) {
            $msgs = array_map(
                fn($e) => "Línea {$e->line}: ".trim($e->message),
                $errs
            );
            throw new \Exception("Error al parsear XML:\n".implode("\n", $msgs));
        }

        // 2) Localizar WorkflowProcess (namespace-agnóstico)
        $wps = $xml->xpath('//*[local-name()="WorkflowProcess"]');
        if (empty($wps)) {
            throw new \Exception("No se encontró ningún WorkflowProcess en {$filePath}");
        }
        /** @var \SimpleXMLElement $wp */
        $wp = $wps[0];
        $attrs = $wp->attributes();

        // 3) Crear Process
        $process = Process::create([
            'name'        => (string) ($attrs['Name'] ?? $attrs['Id']),
            'key'         => (string) ($attrs['Id']   ?? $attrs['Name']),
            'description' => (string) ($wp->Description ?? null),
        ]);

        // 4) Crear Flow
        $flow = Flow::create([
            'process_id' => $process->id,
            'version'    => (string) ($attrs['ProcessVersion'] ?? '1.0'),
            'active'     => true,
        ]);

        // 5) Crear Activities
        $map = [];
        foreach ($wp->xpath('.//*[local-name()="Activities"]/*[local-name()="Activity"]') as $a) {
            $aAttrs  = $a->attributes();
            $rawType = strtolower((string) ($aAttrs['ActivityType'] ?? ''));
            switch ($rawType) {
                case 'start':
                    $type = 'startEvent';
                    break;
                case 'end':
                    $type = 'endEvent';
                    break;
                default:
                    $type = 'userTask';
            }

            $act = Activity::create([
                'flow_id' => $flow->id,
                'name'    => (string) ($aAttrs['Name'] ?? $aAttrs['Id']),
                'type'    => $type,
                'config'  => [],
            ]);

            $map[(string) $aAttrs['Id']] = $act->id;
        }

        // 6) Crear Transitions
        foreach ($wp->xpath('.//*[local-name()="Transitions"]/*[local-name()="Transition"]') as $t) {
            $tAttrs    = $t->attributes();
            $condition = (string) ($tAttrs['ConditionExpression'] ?? null);

            Transition::create([
                'flow_id'            => $flow->id,
                'from_activity_id'   => $map[(string) $tAttrs['From']],
                'to_activity_id'     => $map[(string) $tAttrs['To']],
                'condition'          => $condition,
            ]);
        }

        return $flow;
    }
}
