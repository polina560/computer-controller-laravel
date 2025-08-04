<?php

declare(strict_types=1);

namespace App\MoonShine\Pages;

use MoonShine\Apexcharts\Components\LineChartMetric;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Laravel\Pages\Page;
use MoonShine\UI\Components\Layout\Grid;
use Nette\Utils\Html;

class LogPage extends Page
{
    /**
     * @return array<string, string>
     */
    public function getBreadcrumbs(): array
    {
        return [
            '#' => $this->getTitle(),
        ];
    }

    public function getTitle(): string
    {
        return $this->title ?: 'LogPage';
    }

    /**
     * @return list<ComponentContract>
     */
    protected function components(): iterable
    {

        return [
            Html::make('
            <script>
               document.addEventListener("DOMContentLoaded", function() {
            const now = new Date();
            const startOfDay = new Date(now);
            startOfDay.setHours(now.getHours() - 24, 0, 0, 0);
            startOfDay.setMinutes(now.getMinutes() -  60);
            const endOfDay = new Date(startOfDay);
            endOfDay.setHours(now.getHours() + 25, 0, 0, 0);
            const startOfDayTimestamp = startOfDay.getTime();
            const endOfDayTimestamp = endOfDay.getTime();
            var options = {
                series: [{
                    data: []
                }],
                chart: {
                    type: 'rangeBar',
                    height: 1200,
                },
                plotOptions: {
                    bar: {
                        horizontal: true,
                        rangeBarGroupRows: true
                    }
                },
                xaxis: {
                    type: 'datetime',
                    labels: {

                        format: 'HH:mm',
                        formatter: function (value) {
                            const date = new Date(value);
                            return date.toLocaleTimeString('ru-RU', { hour: '2-digit', minute: '2-digit' });
                        }
                    },
                    min: startOfDayTimestamp,
                    max: endOfDayTimestamp,
                },
                yaxis: {
                    labels: {
                        style: {
                            fontSize: '15px'
                        },
                        formatter: function (value) {
                            return value;
                        }
                    }
                },
                tooltip: {
                    style: {
                        fontSize: '12px',
                        fontFamily: 'Arial, sans-serif',
                        background: '#333333', // Темный фон подсказки
                        color: '#FFFFFF', // Белый цвет текста
                    },
enabled: true,
custom: function({ seriesIndex, dataPointIndex, w }) {
    const data = w.config.series[seriesIndex].data[dataPointIndex];
    const status = data.fillColor === '#00FF00' ? 'Online' : 'Offline';
    const startTime = new Date(data.y[0]).toLocaleTimeString('ru-RU', { hour: '2-digit', minute: '2-digit' });
const endTime = new Date(data.y[1]).toLocaleTimeString('ru-RU', { hour: '2-digit', minute: '2-digit' });

                        // Отображаем временной диапазон
                        return `<div style="color: #000000">${status}: ${startTime} - ${endTime}</div>`;
                        // return `<div style="color: #000000">${data.x}: ${status}</div>`;
                    }
                }
            };

            // Инициализация графика
            var chart = new ApexCharts(document.querySelector("#chart"), options);
            chart.render();

            // Функция для загрузки данных
            function fetchData() {
                fetch('/admin/statistic/get-status-data')
                .then(response => response.json())
                    .then(data => {
                    const validData = data.filter(item => item.x && item.y && item.fillColor);
                        chart.updateSeries([{
                            data: validData
                        }]);
                    })
                    .catch(error => {
                    console.error('Ошибка при загрузке данных:', error);
                });
            }

            // Первоначальная загрузка данных
            fetchData();

            // Обновление данных каждую минуту
            setInterval(fetchData, 60000);
        });
            </script>
        '),
        ];
    }
}
