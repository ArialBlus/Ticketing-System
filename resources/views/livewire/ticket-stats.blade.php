<div class="p-6 bg-white rounded-lg shadow">
    <div class="mb-4">
        <h2 class="text-lg font-semibold text-gray-900">Tickets por Estado</h2>
    </div>
    
    @if($error)
        <div class="alert alert-warning">
            {{ $error }}
        </div>
    @else
        <div class="w-full h-96 relative">
            <canvas id="ticket-status-chart-{{ $id }}"></canvas>
        </div>
    @endif
    
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // inicializar el grafico cuando el componente esta listo
        if (document.getElementById('ticket-status-chart-{{ $id }}')) {
            new Chart(document.getElementById('ticket-status-chart-{{ $id }}'), {
                type: 'pie',
                data: {
                    labels: @json($chartLabels),
                    datasets: [{
                        data: @json($chartData),
                        backgroundColor: [
                            'rgba(54, 162, 235, 0.8)',
                            'rgba(255, 206, 86, 0.8)',
                            'rgba(75, 192, 192, 0.8)'
                        ],
                        borderColor: [
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                font: {
                                    size: 14
                                }
                            }
                        }
                    }
                }
            });
        }
    </script>
    @endpush
</div>