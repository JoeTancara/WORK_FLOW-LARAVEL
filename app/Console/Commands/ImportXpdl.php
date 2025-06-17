<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\XMLParser\XpdlParser;

class ImportXpdl extends Command
{
    protected $signature   = 'import:xpdl {file : Ruta al archivo .xpdl}';
    protected $description = 'Importa un proceso XPDL y lo guarda en la BD';

    public function handle(XpdlParser $parser)
    {
        $file = base_path($this->argument('file'));
        if (! file_exists($file)) {
            return $this->error("No existe: {$file}");
        }

        $flow = $parser->parse($file);
        $this->info("Flujo importado con ID {$flow->id} (Process ID {$flow->process_id}).");
    }
}
