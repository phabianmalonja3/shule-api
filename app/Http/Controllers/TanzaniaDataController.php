<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class TanzaniaDataController extends Controller
{
    // Full path to your Python script
    protected $pythonScriptPath = '/home/shulemisac/shule-api/utils/geo-location.py';

    /**
     * Fetch all regions
     */
    public function fetchRegions()
    {
        $process = new Process(['python3', $this->pythonScriptPath]);

        try {
            $process->mustRun();
            $output = $process->getOutput();
            return Response::json(json_decode($output, true));
        } catch (ProcessFailedException $exception) {
            return Response::json([
                'error' => 'Failed to fetch regions from Python script',
                'message' => $exception->getMessage()
            ], 500);
        }
    }

    /**
     * Fetch districts for a given region
     */
    public function getDistricts(Request $request)
    {
        $region = $request->query('region');
        if (!$region) {
            return Response::json(['error' => 'Region parameter is required'], 400);
        }

        $process = new Process(['python3', $this->pythonScriptPath, $region]);

        try {
            $process->mustRun();
            $output = $process->getOutput();
            return Response::json(json_decode($output, true));
        } catch (ProcessFailedException $exception) {
            return Response::json([
                'error' => 'Failed to fetch districts from Python script',
                'message' => $exception->getMessage()
            ], 500);
        }
    }

    /**
     * Fetch wards for a given region and district
     */
    public function getWards(Request $request)
    {
        $region = $request->query('region');
        $district = $request->query('district');

        if (!$region || !$district) {
            return Response::json(['error' => 'Region and District parameters are required'], 400);
        }

        $process = new Process(['python3', $this->pythonScriptPath, $region, $district]);

        try {
            $process->mustRun();
            $output = $process->getOutput();
            return Response::json(json_decode($output, true));
        } catch (ProcessFailedException $exception) {
            return Response::json([
                'error' => 'Failed to fetch wards from Python script',
                'message' => $exception->getMessage()
            ], 500);
        }
    }
}

