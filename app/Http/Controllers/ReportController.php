<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request, ReportService $reports): View
    {
        $report = $reports->forUser($request->user());

        $layout = match ($request->user()->role) {
            'learner' => 'learner.layout',
            'teacher' => 'teacher.layout',
            'parents' => 'parents.layout',
            'admin' => 'admin.layout',
            default => 'learner.layout',
        };

        return view('reports.index', compact('report', 'layout'));
    }

    public function export(Request $request, ReportService $reports): StreamedResponse
    {
        $report = $reports->forUser($request->user());
        $rows = $report['rows'];
        $filename = 'signgyaan-report-'.now()->format('Y-m-d-His').'.csv';

        return response()->streamDownload(function () use ($rows): void {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");

            if ($rows->isEmpty()) {
                fputcsv($out, ['No report rows available']);
                fclose($out);
                return;
            }

            fputcsv($out, array_keys($rows->first()));
            foreach ($rows as $row) {
                fputcsv($out, array_values($row));
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
