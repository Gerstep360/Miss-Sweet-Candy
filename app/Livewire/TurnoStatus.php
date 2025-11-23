<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\TurnoCaja;
use Illuminate\Support\Facades\Auth;

class TurnoStatus extends Component
{
    public bool $hayTurno = false;
    public bool $esMiTurno = false;
    public ?array $turno = null;

    public function mount(): void
    {
        $this->loadTurno();
    }

    public function refreshTurno(): void
    {
        $this->loadTurno();
    }

    protected function loadTurno(): void
    {
        $turnoActivo = TurnoCaja::turnoActivo();

        $this->hayTurno  = (bool) $turnoActivo;
        $this->esMiTurno = $turnoActivo && $turnoActivo->cajero_id === Auth::id();

        if ($turnoActivo) {
            $this->turno = [
                'id'                  => $turnoActivo->id,
                'cajero'              => [
                    'id'   => optional($turnoActivo->cajero)->id,
                    'name' => optional($turnoActivo->cajero)->name,
                ],
                'tiempo_transcurrido' => $turnoActivo->tiempo_transcurrido,
                'duracion_formateada' => $turnoActivo->duracion_formateada,
                'monto_inicial'       => (float) $turnoActivo->monto_inicial,
            ];
        } else {
            $this->turno = null;
        }
    }

    public function render()
    {
        return view('livewire.turno-status');
    }
}
