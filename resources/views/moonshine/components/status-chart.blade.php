@push('styles')
    <style>
        #status-chart {
            background: white;
            border-radius: 0.5rem;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        #chart {
            min-height: 500px;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const chartData = @json($chartData);

            const options = {
                series: chartData.map(item => ({
                    name: item.name,
                    data: item.data
                })),
                chart: {
                    type: 'line',
                    height: '100%',
                    toolbar: { show: true },
                    zoom: { enabled: true }
                },
                stroke: { width: 2 },
                markers: { size: 5 },
                xaxis: {
                    type: 'datetime',
                    labels: { format: 'dd MMM HH:mm' }
                },
                yaxis: {
                    min: 0,
                    max: 1,
                    labels: {
                        formatter: function(val) {
                            return val === 1 ? 'Online' : 'Offline';
                        }
                    }
                },
                tooltip: {
                    custom: function({ seriesIndex, dataPointIndex, w }) {
                        const series = w.globals.series[seriesIndex];
                        const point = series[dataPointIndex];
                        return `
                    <div class="p-2 bg-white rounded shadow">
                        <strong>${w.globals.seriesNames[seriesIndex]}</strong>
                        <div>Status: ${point.y ? 'Online' : 'Offline'}</div>
                        <div>${new Date(point.x).toLocaleString()}</div>
                    </div>
                `;
                    }
                },
                colors: ['#3B82F6', '#10B981', '#EF4444', '#F59E0B']
            };

            const chart = new ApexCharts(
                document.querySelector("#chart"),
                options
            );
            chart.render();

            // Auto-refresh every 5 minutes
            setInterval(() => {
                fetch('/admin/computer-logs/chart-data')
                    .then(r => r.json())
                    .then(data => {
                        chart.updateSeries(data.map(item => ({
                            name: item.name,
                            data: item.data
                        })));
                    });
            }, 300000);
        });
    </script>
@endpush

<div id="status-chart">
    <h2 class="text-xl font-semibold mb-4">Статус компьютеров</h2>
    <div id="chart"></div>
</div>
