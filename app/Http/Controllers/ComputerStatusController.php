<?php

namespace App\Http\Controllers;

use App\Models\Computer;
use App\Models\ComputerLog;

class ComputerStatusController extends Controller
{
    public function getData()
    {
        $computers = Computer::query()
            ->orderBy('full_name')
            ->select(['id', 'computer_name', 'full_name'])
            ->get()
            ->toArray();

        $data = [];

        foreach ($computers as $computer) {
            $logs = ComputerLog::query()
                ->where('computer_id', $computer['id'])
                ->orderBy('updated_at', 'ASC')
                ->select(['status', 'updated_at'])
                ->get()
                ->toArray();

            $previousStatus = null;
            $startTime = null;

            foreach ($logs as $log) {
                if ($previousStatus === null) {
                    $previousStatus = $log['status'];
                    $startTime = strtotime($log['updated_at']);
                } elseif ($previousStatus !== $log['status']) {
                    $data[] = [
                        'x' => explode(' ', (string) $computer['full_name'])[0],
                        'y' => [
                            $startTime * 1000,
                            strtotime($log['updated_at']) * 1000,
                        ],
                        'fillColor' => $previousStatus ? '#8BED3B' : '#ED3B3B',
                    ];
                    $previousStatus = $log['status'];
                    $startTime = strtotime($log['updated_at']);
                }
            }

            if ($previousStatus !== null) {
                $data[] = [
                    'x' => explode(' ', (string) $computer['full_name'])[0],
                    'y' => [
                        $startTime * 1000,
                        now()->timestamp * 1000,
                    ],
                    'fillColor' => $previousStatus ? '#8BED3B' : '#ED3B3B',
                ];
            }
        }

        return response()->json($data);
    }
}
