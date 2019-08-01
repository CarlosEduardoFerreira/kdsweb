<?php

namespace App\Http\Controllers\Admin;

// require_once('/SimpleExcel/SimpleExcel.php');

use App\Http\Controllers\Controller;
use App\Models\LicenseLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class ReportController extends Controller {
    
    public function __construct() {
        $this->middleware('auth');
    }

    
    public function index() {

        $me = Auth::user();
        
        return view('admin.reports', ['me' => $me]);
    }

    
    public function getLicensesQuantityByMonth(Request $request) {
        
        if(empty($request->storeGuid) or empty($request->month)) {
            return -1;
        }
        
        $storeGuid = $request->storeGuid;
        $month = $request->month;
        
        $monthArray = explode('-', $month); // $month = yyyy-mm
        
        if(empty($monthArray) or count($monthArray) != 2) {
            return -2;
        }
        
        $y = $monthArray[0];
        $m = $monthArray[1];
        
        $monthStart = mktime(0, 0, 0, $m, 1, $y);
        $monthEnd = mktime(23, 59, 59, $m + 1, 0, $y);
        
        $settings = DB::table('settings')->where(['store_guid' => $storeGuid])->first();
        if(empty($settings)) {
            return 0;
        }
        
        $tenDays = 864000; // seconds (1 day = 86400 , 10 days = 864000 , 31 days = 2678400)
        
        $licenseLogAux = LicenseLog::where('store_guid', '=', $storeGuid)
        ->where('update_time', '<', $monthStart)
        ->orderBy('update_time')->get()->last();
        
        if(empty($licenseLogAux)) {
            $licenseLogsMonthFirst = LicenseLog::where('store_guid', '=', $storeGuid)
            ->where('update_time', '>=', $monthStart)
            ->where('update_time', '<=', $monthEnd)
            ->orderBy('update_time')->get()->first();
            
            if(empty($licenseLogsMonthFirst)) {
                if(!empty($settings->create_time)) {
                    if(time() - $settings->create_time >= $tenDays) {
                        return $settings->licenses_quantity;
                    }
                }
                return 0;
            } else {
                $licenseLogAux = $licenseLogsMonthFirst;
            }
        }
        
        $licenseLogs = LicenseLog::where('store_guid', '=', $storeGuid)
        ->where('update_time', '>=', $monthStart)
        ->where('update_time', '<=', $monthEnd)
        ->orderBy('update_time')->get();
        
        foreach($licenseLogs as $licenseLog) {
            $hasMoreThan10Days = ($licenseLog->update_time - $licenseLogAux->update_time) >= $tenDays;
            if($hasMoreThan10Days and $licenseLog->quantity >= $licenseLogAux->quantity) {
                $licenseLogAux = $licenseLog;
            }
        }
        
        return $licenseLogAux->quantity;
    }
    
    
    public function getStatementListExcelFile(Request $request) {
        $me = Auth::user();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $writer = new Xlsx($spreadsheet);
        
        $stores = ReportCostByStatementController::getStoresForStatements($me, $request);
        
        $sheet->setCellValue('A1', 'Reseller');
        $sheet->setCellValue('B1', 'Store Group');
        $sheet->setCellValue('C1', 'Store');
        $sheet->setCellValue('D1', 'License Type');
        $sheet->setCellValue('E1', 'Stations Quantity');
        $sheet->setCellValue('F1', 'Price per License');
        $sheet->setCellValue('G1', 'Total Price');
        
        $sheet->getStyle("A1:G1")->getFont()->setBold(true);
        
        $row = 2;
        foreach($stores as $store) {
            $request->storeGuid = $store->store_guid;
            
            $licensesQuantity = $store->live ? $this->getLicensesQuantityByMonth($request) : 0;
            
            $sheet->setCellValue('A' . $row, $store->resellerBName);
            $sheet->setCellValue('B' . $row, $store->storegroupBName);
            $sheet->setCellValue('C' . $row, $store->storeBName);
            $sheet->setCellValue('D' . $row, $store->planName);
            $sheet->setCellValue('E' . $row, $licensesQuantity);
            
            $sheet->getStyle('F' . $row)->getNumberFormat()->setFormatCode('0.00');
            $sheet->setCellValue('F' . $row, number_format($store->planCost, 2, '.', ''));
            
            $sheet->getStyle('G' . $row)->getNumberFormat()->setFormatCode('0.00');
            $sheet->setCellValue('G' . $row, number_format($licensesQuantity * $store->planCost, 2, '.', ''));
            
            $row++;
        }
        
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setAutoSize(true);
        $sheet->getColumnDimension('G')->setAutoSize(true);

        $file = 'downloads/statement-list-' . time() . '.xlsx';
        
        $writer->save($file);

        return "/" . $file;
    }
    
    
    public function downloadCompleted(Request $request) {
        if(empty($request->file)) {
            return;
        }
        
        $file = ltrim($request->file, '/');
        
        $downloadFolder = explode('/', $file);
        
        if($downloadFolder[0] == 'downloads') {
            $fileLink = fopen($file, 'w') or die("can't open file");
            fclose($fileLink);
            unlink($file) or die("Couldn't delete file");
        }
    }
    
}












