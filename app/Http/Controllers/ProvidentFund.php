<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use App\Models\PfDeposit;
use App\Models\Gratuity;
use App\Models\PfInterest;
use App\Models\GratuityInterest;
use App\Models\Employee;
use App\Models\PaySlip;

class ProvidentFund extends Controller
{
    public function providentFunds()
    {
        $employees = Employee::all();
        $providentFunds = [];
    
        foreach ($employees as $employee) {
            $totalProvident = PfDeposit::where('employee_id', $employee->id)->get();
            $totalPF = $totalProvident->sum('total_pf');
            $totalInterest = PfInterest::where('employee_id', $employee->id)->get();
            $totalPI = $totalInterest->sum('interest_amount');
            $fromDate = $totalProvident->min('provident_month');
            $toDate = $totalProvident->max('provident_month');
    
            $fromDateFormatted = $fromDate ? date('F, Y', strtotime($fromDate)) : '-';
            $toDateFormatted = $toDate ? date('F, Y', strtotime($toDate)) : '-';
    
            $providentFunds[] = (object) [
                'employee_id' => $employee->id,
                'name' => $employee->name,
                'total_pf' => $totalPF,
                'totalPI' => $totalPI,
                'from_date' => $fromDateFormatted,
                'to_date' => $toDateFormatted,
            ];
        }
        return view('provident-fund.index', compact('providentFunds'));
    }
    
    public function employeeProvidentFund($id){
        $id = Crypt::decrypt($id);
        $employeeName = Employee::find($id)->name;
        $providentFunds = PfDeposit::where('employee_id', $id)->get();
        return view('provident-fund.employee-provident',compact('providentFunds','employeeName'));
    }

    public function providentFundInterests()
    {
        $employees = Employee::all();
        $providentInterests = [];
    
        foreach ($employees as $employee) {
            $totalInterest = PfInterest::where('employee_id', $employee->id)->get();
            $totalPI = $totalInterest->sum('interest_amount');
            $fromDate = $totalInterest->min('interest_month');
            $toDate = $totalInterest->max('interest_month');
    
            $fromDateFormatted = $fromDate ? date('F, Y', strtotime($fromDate)) : '-';
            $toDateFormatted = $toDate ? date('F, Y', strtotime($toDate)) : '-';
    
            $providentInterests[] = (object) [
                'employee_id' => $employee->id,
                'name' => $employee->name,
                'total_pi' => $totalPI,
                'from_date' => $fromDateFormatted,
                'to_date' => $toDateFormatted,
            ];
        }
        return view('provident-fund.interest',compact('providentInterests'));
    }

    public function providentFundEmployeeInterests($id){
        $id = Crypt::decrypt($id);
        $employeeName = Employee::find($id)->name;
        $providentInterests = PfInterest::where('employee_id', $id)->get();
        return view('provident-fund.employee-interest',compact('providentInterests','employeeName'));
    }
    
    public function gratuity()
    {
        $employees = Employee::all();
        $gratuities = [];
    
        foreach ($employees as $employee) {
            $totalProvident = Gratuity::where('employee_id', $employee->id)->get();
            $totalGF = $totalProvident->sum('amount');
            $totalInterest = GratuityInterest::where('employee_id', $employee->id)->get();
            $totalGI = $totalInterest->sum('interest_amount');
            $fromDate = $totalProvident->min('gratuity_month');
            $toDate = $totalProvident->max('gratuity_month');
    
            $fromDateFormatted = $fromDate ? date('F, Y', strtotime($fromDate)) : '-';
            $toDateFormatted = $toDate ? date('F, Y', strtotime($toDate)) : '-';
    
            $gratuities[] = (object) [
                'employee_id' => $employee->id,
                'name' => $employee->name,
                'total_gf' => $totalGF,
                'total_gi' => $totalGI,
                'from_date' => $fromDateFormatted,
                'to_date' => $toDateFormatted,
            ];
        }
        return view('provident-fund.gratuity',compact('gratuities'));
    }

    public function employeeGratuity($id){
        $id = Crypt::decrypt($id);
        $employeeName = Employee::find($id)->name;
        $gratuities = Gratuity::where('employee_id', $id)->get();
        return view('provident-fund.employee-gratuity',compact('gratuities','employeeName'));
    }

    public function gratuityInterest(){

        $employees = Employee::all();
        $gratuityInterests = [];
    
        foreach ($employees as $employee) {
            $totalInterest = GratuityInterest::where('employee_id', $employee->id)->get();
            $totalGI = $totalInterest->sum('interest_amount');
            $fromDate = $totalInterest->min('interest_month');
            $toDate = $totalInterest->max('interest_month');
    
            $fromDateFormatted = $fromDate ? date('F, Y', strtotime($fromDate)) : '-';
            $toDateFormatted = $toDate ? date('F, Y', strtotime($toDate)) : '-';
    
            $gratuityInterests[] = (object) [
                'employee_id' => $employee->id,
                'name' => $employee->name,
                'total_gi' => $totalGI,
                'from_date' => $fromDateFormatted,
                'to_date' => $toDateFormatted,
            ];
        }

        return view('provident-fund.gratuityInterest',compact('gratuityInterests'));
    }

    public function taxReport(){
        $employees = Employee::all();
        $taxReport = [];
    
        foreach ($employees as $employee) {
            $taxData=PaySlip::where('employee_id','=',$employee->id)->where('salary_month', date('Y-m'))->first();

            $taxReport[] = (object) [
                'employee_id' => $employee->id,
                'name' => $employee->name,
                'basic_salary' => $taxData->basic_salary,
                'allowance' => $employee->allowanceCommisions() + $employee->overtimeOthers(),
                'tax' => $taxData->tax,
            ];
        }

        return view('provident-fund.taxReport',compact('taxReport'));
    }

    public function gratuityEmployeeInterest($id){
        $id = Crypt::decrypt($id);
        $employeeName = Employee::find($id)->name;
        $gratuityInterests = GratuityInterest::where('employee_id', $id)->get();
        return view('provident-fund.employee-gratuityInterest',compact('gratuityInterests','employeeName'));
    }

}

