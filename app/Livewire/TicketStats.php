<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Ticket;
use Illuminate\Support\Facades\Log;

class TicketStats extends Component
{
    public $chartData = [];
    public $chartLabels = [];
    public $error = null;
    public $loading = true;
    public $readyToLoad = false;
    public $id;

    public function mount()
    {
        $this->id = uniqid();
        $this->loading = true;
        $this->readyToLoad = false;
        
        // Load data immediately
        $this->loadData();
    }

    public function loadData()
    {
        try {
            Log::info('Iniciando carga de datos para TicketStats');
            
            // Get ticket count by status name
            $statusCounts = Ticket::selectRaw('statuses.name, count(*) as total')
                ->join('statuses', 'tickets.status_id', '=', 'statuses.id')
                ->groupBy('statuses.name')
                ->pluck('total', 'statuses.name')
                ->toArray();

            Log::info('Query results:', ['status_counts' => $statusCounts]);

            // Labels and data for the chart
            $this->chartLabels = ['Abierto', 'En Proceso', 'Cerrado'];
            $this->chartData = [
                $statusCounts['Abierto'] ?? 0,
                $statusCounts['En Proceso'] ?? 0,
                $statusCounts['Cerrado'] ?? 0
            ];

            Log::info('Chart data prepared:', [
                'labels' => $this->chartLabels,
                'data' => $this->chartData
            ]);

            $this->error = null;
            $this->loading = false;
            $this->readyToLoad = true;

        } catch (\Exception $e) {
            Log::error('Error loading ticket stats:', ['error' => $e->getMessage()]);
            $this->error = 'Error al cargar los datos del gráfico: ' . $e->getMessage();
            $this->loading = false;
        }
    }

    public function render()
    {
        return view('livewire.ticket-stats');
    }
}
