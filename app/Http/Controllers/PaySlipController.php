<?php

namespace App\Http\Controllers;

use App\Exports\PayslipExport;
use App\Models\Allowance;
use App\Models\Commission;
use App\Models\Employee;
use App\Models\Loan;
use App\Models\OtherPayment;
use App\Models\Overtime;
use App\Models\PaySlip;
use App\Models\SaturationDeduction;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\PfDeposit;
use App\Models\Gratuity;
use App\Models\PfInterest;
use App\Models\GratuityInterest;
use Carbon\Carbon; # A
use Illuminate\Support\Facades\Http; # A
use Illuminate\Support\Facades\Redirect; # A
use Session;
use App\Models\Approval;
use App\Models\ApprovalStatus;
use DB;
use Barryvdh\DomPDF\Facade\Pdf;

class PaySlipController extends Controller
{

    public function index()
    {
        // if(\Auth::user()->can('manage pay slip') || \Auth::user()->type != 'client' || \Auth::user()->type != 'company')
        // {
        $employees = Employee::where(
            [
                'created_by' => \Auth::user()->creatorId(),
            ]
        )->first();

        $month = [
            '01' => 'JAN',
            '02' => 'FEB',
            '03' => 'MAR',
            '04' => 'APR',
            '05' => 'MAY',
            '06' => 'JUN',
            '07' => 'JUL',
            '08' => 'AUG',
            '09' => 'SEP',
            '10' => 'OCT',
            '11' => 'NOV',
            '12' => 'DEC',
        ];

        $year = [
            '2020' => '2020',
            '2021' => '2021',
            '2022' => '2022',
            '2023' => '2023',
            '2024' => '2024',
            '2025' => '2025',
            '2026' => '2026',
            '2027' => '2027',
            '2028' => '2028',
            '2029' => '2029',
            '2030' => '2030',
        ];

        return view('payslip.index', compact('employees', 'month', 'year'));
        // }
        // else
        // {
        //     return redirect()->back()->with('error', __('Permission denied.'));
        // }
    }

    public function create()
    {
        //
    }

    // Payslip generate - 2024
    public function store(Request $request)
    {
        $validator = \Validator::make(
            $request->all(),
            [
                'month' => 'required',
                'year' => 'required',
            ]
        );

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }

        $month = $request->month;
        $year = $request->year;

        $formate_month_year = $year . '-' . $month;
        $validatePaysilp = PaySlip::where('salary_month', '=', $formate_month_year)->where('created_by', \Auth::user()->creatorId())->pluck('employee_id');
        $payslip_employee = Employee::where('created_by', \Auth::user()->creatorId())->where('company_doj', '<=', date($year . '-' . $month . '-t'))->count();

        $totalPayslip = (int) count($validatePaysilp);
        $totalEmp = (int) $payslip_employee;

        if ($totalEmp > 0 && $totalEmp == $totalPayslip) {
            // already paid
            return redirect()->route('payslip.index')->with('error', __('Payslip Already created.'));
        }

        if ($payslip_employee > count($validatePaysilp)) {
            $employees = Employee::where('created_by', \Auth::user()->creatorId())->whereNotIn('employee_id', $validatePaysilp)->get();

            $employeesSalary = Employee::where('created_by', \Auth::user()->creatorId())->where('salary', '<=', 0)->first();

            if (!empty($employeesSalary)) {
                return redirect()->route('payslip.index')->with('error', __('Please set employee salary.'));
            }

            $settings = Utility::settings();

            foreach ($employees as $employee) {
                // generated payslip will skipped
                $genPayslip = PaySlip::where('salary_month', $formate_month_year)
                    ->select('employee_id')
                    ->where('employee_id', '=', $employee->id)
                    ->where('created_by', \Auth::user()->creatorId())
                    ->first();

                if (!$genPayslip && $employee->company_doj != '') {

                    // employee have no payslip for this month
                    $totalProvident = PfDeposit::where('employee_id', $employee->id)->sum('total_pf');
                    $totalInterest = PfInterest::where('employee_id', $employee->id)->sum('interest_amount');

                    $totalPFI = $totalProvident + $totalInterest;

                    if ($totalPFI > 0) {
                        PfInterest::create([
                            'employee_id' => $employee->id,
                            'total_provident' => $totalPFI,
                            'interest_type' => $settings['provident_interest_type'],
                            'interest_value' => $settings['provident_interest_value'],
                            'interest_amount' => $settings['provident_interest_type'] === "fixed" ? $settings['provident_interest_value'] : ($totalPFI * $settings['provident_interest_value']) / 100,
                            'interest_month' => $formate_month_year,
                        ]);
                    }

                    if ($settings['own_provident_type'] == 'fixed') {
                        $finalSalary = $employee->get_net_salary() - $settings['own_provident_value'];
                        $own_provident_fund = $settings['own_provident_value'];
                    } else if ($settings['own_provident_type'] == 'percentage') {
                        $deduction = ($employee->salary * $settings['own_provident_value']) / 100;
                        $finalSalary = $employee->get_net_salary() - $deduction;
                        $own_provident_fund = $deduction;
                    }

                    if ($settings['organization_provident_type'] == 'fixed') {
                        $organization_provident_fund = $settings['organization_provident_value'];
                    } else if ($settings['organization_provident_type'] == 'percentage') {
                        $organization_provident_fund = ($employee->salary * $settings['organization_provident_value']) / 100;
                    }

                    PfDeposit::create([
                        'employee_id' => $employee->id,
                        'own_pf_type' => $settings['own_provident_type'],
                        'own_pf_value' => $settings['own_provident_value'],
                        'own_pf' => $own_provident_fund,
                        'organization_pf_type' => $settings['organization_provident_type'],
                        'organization_pf_value' => $settings['organization_provident_value'],
                        'organization_pf' => $organization_provident_fund,
                        'total_pf' => $own_provident_fund + $organization_provident_fund,
                        'provident_month' => $formate_month_year
                    ]);


                    $totalGratuity = Gratuity::where('employee_id', $employee->id)->sum('amount');
                    $totalGratuityInterest = GratuityInterest::where('employee_id', $employee->id)->sum('interest_amount');

                    $totalGI = $totalGratuity + $totalGratuityInterest;
                    if ($totalGI > 0) {
                        GratuityInterest::create([
                            'employee_id' => $employee->id,
                            'total_gratuity' => $totalGI,
                            'interest_type' => $settings['gratuity_interest_type'],
                            'interest_value' => $settings['gratuity_interest_value'],
                            'interest_amount' => $settings['gratuity_interest_type'] === "fixed" ? $settings['gratuity_interest_value'] : ($totalGI * $settings['gratuity_interest_value']) / 100,
                            'interest_month' => $formate_month_year,
                        ]);
                    }


                    if ($settings['gratuity_type'] == 'fixed') {
                        $gratuity_amount = $settings['gratuity_value'];
                    } else if ($settings['gratuity_type'] == 'percentage') {
                        $gratuity_amount = ($employee->salary * $settings['gratuity_value']) / 100;
                    }

                    Gratuity::create([
                        'employee_id' => $employee->id,
                        'gratuity_type' => $settings['gratuity_type'],
                        'gratuity_value' => $settings['gratuity_value'],
                        'amount' => $gratuity_amount,
                        'gratuity_month' => $formate_month_year
                    ]);

                    //Tax Calculation

                    // if($settings['tax_calculation']==='on'){
                    //     $monthlySalary=$employee->salary + $employee->allowanceCommisions();
                    //     $totalYearlyIncome = ($monthlySalary * 12) + $employee->overtimeOthers();
                    //     $taxFreeIncome = min($totalYearlyIncome / 3, 450000);
                    //     $taxableIncome = $totalYearlyIncome - $taxFreeIncome;
                    //     $taxAmount = $this->calculateTax($taxableIncome);
                    //     $monthlyTax = $taxAmount/12;
                    // }
                    // else{
                    //     $monthlyTax = 0;
                    // }

                    $monthlyTax = 0;

                    $payslipEmployee = new PaySlip();
                    $payslipEmployee->employee_id = $employee->id;
                    $payslipEmployee->net_payble = $finalSalary - $monthlyTax;
                    $payslipEmployee->salary_month = $formate_month_year;
                    $payslipEmployee->status = 0;
                    $payslipEmployee->basic_salary = !empty($employee->salary) ? $employee->salary : 0;
                    $payslipEmployee->allowance = Employee::allowance($employee->id);
                    $payslipEmployee->commission = Employee::commission($employee->id);
                    $payslipEmployee->loan = Employee::loan($employee->id);
                    $payslipEmployee->saturation_deduction = Employee::saturation_deduction($employee->id);
                    $payslipEmployee->other_payment = Employee::other_payment($employee->id);
                    $payslipEmployee->overtime = Employee::overtime($employee->id);
                    $payslipEmployee->provident_fund = $own_provident_fund;
                    $payslipEmployee->tax = $monthlyTax;
                    $payslipEmployee->approval = 'Pending';
                    $payslipEmployee->created_by = \Auth::user()->creatorId();
                    $payslipEmployee->save();


                    //For Notification
                    $setting = Utility::settings(\Auth::user()->creatorId());
                    $payslipNotificationArr = [
                        'year' => $formate_month_year,
                    ];
                    //Slack Notification
                    if (isset($setting['payslip_notification']) && $setting['payslip_notification'] == 1) {
                        Utility::send_slack_msg('new_monthly_payslip', $payslipNotificationArr);
                    }

                    //Telegram Notification
                    if (isset($setting['telegram_payslip_notification']) && $setting['telegram_payslip_notification'] == 1) {
                        Utility::send_telegram_msg('new_monthly_payslip', $payslipNotificationArr);
                    }
                    //webhook
                    $module = 'New Monthly Payslip';
                    $webhook = Utility::webhookSetting($module);

                    if ($webhook) {
                        $parameter = json_encode($payslipEmployee);
                        $status = Utility::WebhookCall($webhook['url'], $parameter, $webhook['method']);

                        if ($status == true) {
                            return redirect()->back()->with('success', __('Payslip successfully created.'));
                        } else {
                            return redirect()->back()->with('error', __('Webhook call failed.'));
                        }
                    }

                }



            }

            return redirect()->route('payslip.index')->with('success', __('Payslip successfully created.'));
        } else {
            return redirect()->route('payslip.index')->with('error', __('Payslip Already created.'));
        }


        /*

                $totalPayslip = (int)count($validatePaysilp);
                $totalEmp = (int)$payslip_employee;

                if($totalEmp>0 && $totalEmp==$totalPayslip){
                    // already paid
                    return redirect()->route('payslip.index')->with('error', __('Payslip Already created.'));
                }

                if($payslip_employee > count($validatePaysilp))
                {
                    $employees = Employee::where('created_by', \Auth::user()->creatorId())->where('company_doj', '<=', date($year . '-' . $month . '-t'))->whereNotIn('employee_id', $validatePaysilp)->get();

                    $employeesSalary = Employee::where('created_by', \Auth::user()->creatorId())->where('salary', '<=', 0)->first();

                    if(!empty($employeesSalary))
                    {
                        return redirect()->route('payslip.index')->with('error', __('Please set employee salary.'));
                    }

                    foreach($employees as $employee)
                    {
                        // Start - Coming from PF Settings
                        $totalProvident = PfDeposit::where('employee_id', $employee->id)->sum('total_pf');
                        $totalInterest = PfInterest::where('employee_id', $employee->id)->sum('interest_amount');

                        $totalPFI=$totalProvident+$totalInterest;

                        if($totalPFI > 0){
                            PfInterest::create([
                                'employee_id' => $employee->id,
                                'total_fund' => $totalPFI,
                                'interest_type' => $settings['provident_interest_type'],
                                'interest_value' => $settings['provident_interest_value'],
                                'interest_amount' => $settings['provident_interest_type'] === "fixed" ? $settings['provident_interest_value'] : ($totalPFI * $settings['provident_interest_value']) / 100,
                                'interest_month' => $formate_month_year,
                            ]);
                        }

                        if($settings['own_provident_type'] == 'fixed'){
                            $finalSalary = $employee->get_net_salary() - $settings['own_provident_value'];
                            $own_provident_fund=$settings['own_provident_value'];
                        }
                        else if($settings['own_provident_type'] == 'percentage'){
                            $deduction = ($employee->salary * $settings['own_provident_value']) / 100;
                            $finalSalary = $employee->get_net_salary() - $deduction;
                            $own_provident_fund=$deduction;
                        }

                        if($settings['organization_provident_type'] == 'fixed'){
                            $organization_provident_fund=$settings['organization_provident_value'];
                        }
                        else if($settings['organization_provident_type'] == 'percentage'){
                            $organization_provident_fund = ($employee->salary * $settings['organization_provident_value']) / 100;
                        }

                        PfDeposit::create([
                            'employee_id' => $employee->id,
                            'own_pf_type' => $settings['own_provident_type'],
                            'own_pf_value' => $settings['own_provident_value'],
                            'own_pf' => $own_provident_fund,
                            'organization_pf_type' => $settings['organization_provident_type'],
                            'organization_pf_value' => $settings['organization_provident_value'],
                            'organization_pf' => $organization_provident_fund,
                            'total_pf' => $own_provident_fund + $organization_provident_fund,
                            'provident_month' => $formate_month_year
                        ]);


                        $totalGratuity = Gratuity::where('employee_id', $employee->id)->sum('amount');
                        $totalGratuityInterest = GratuityInterest::where('employee_id', $employee->id)->sum('interest_amount');

                        $totalGI=$totalGratuity+$totalGratuityInterest;
                        if($totalGI > 0){
                            GratuityInterest::create([
                                'employee_id' => $employee->id,
                                'total_gratuity' => $totalGI,
                                'interest_type' => $settings['gratuity_interest_type'],
                                'interest_value' => $settings['gratuity_interest_value'],
                                'interest_amount' => $settings['gratuity_interest_type'] === "fixed" ? $settings['gratuity_interest_value'] : ($totalGI * $settings['gratuity_interest_value']) / 100,
                                'interest_month' => $formate_month_year,
                            ]);
                        }


                        if($settings['gratuity_type'] == 'fixed'){
                            $gratuity_amount=$settings['gratuity_value'];
                        }
                        else if($settings['gratuity_type'] == 'percentage'){
                            $gratuity_amount = ($employee->salary * $settings['gratuity_value']) / 100;
                        }

                        Gratuity::create([
                            'employee_id' => $employee->id,
                            'gratuity_type' => $settings['gratuity_type'],
                            'gratuity_value' => $settings['gratuity_value'],
                            'amount' => $gratuity_amount,
                            'gratuity_month' => $formate_month_year
                        ]);

                        //Tax Calculation

                        if($settings['tax_calculation']==='on'){
                            $monthlySalary=$employee->salary + $employee->allowanceCommisions();
                            $totalYearlyIncome = ($monthlySalary * 12) + $employee->overtimeOthers();
                            $taxFreeIncome = min($totalYearlyIncome / 3, 450000);
                            $taxableIncome = $totalYearlyIncome - $taxFreeIncome;
                            $taxAmount = $this->calculateTax($taxableIncome);
                            $monthlyTax = $taxAmount/12;
                        }
                        else{
                            $monthlyTax = 0;
                        }
                        // End - Coming from PF Settings

                        // generated payslip will skipped
                        $genPayslip = PaySlip::where('salary_month', $formate_month_year)
                            ->select('employee_id')
                            ->where('employee_id','=',$employee->id)
                            ->where('created_by', \Auth::user()->creatorId())
                            ->first();

                        if(!$genPayslip){

                            $payslipEmployee                       = new PaySlip();
                            $payslipEmployee->employee_id          = $employee->id;
                            $payslipEmployee->net_payble           = $employee->get_net_salary();
                            $payslipEmployee->salary_month         = $formate_month_year;
                            $payslipEmployee->status               = 0;
                            $payslipEmployee->basic_salary         = !empty($employee->salary) ? $employee->salary : 0;
                            $payslipEmployee->allowance            = Employee::allowance($employee->id);
                            $payslipEmployee->commission           = Employee::commission($employee->id);
                            $payslipEmployee->loan                 = Employee::loan($employee->id);
                            $payslipEmployee->saturation_deduction = Employee::saturation_deduction($employee->id);
                            $payslipEmployee->other_payment        = Employee::other_payment($employee->id);
                            $payslipEmployee->overtime             = Employee::overtime($employee->id);
                            $payslipEmployee->created_by           = \Auth::user()->creatorId();
                            $payslipEmployee->save();


                            $payslipEmployee                       = new PaySlip();
                            $payslipEmployee->employee_id          = $employee->id;
                            $payslipEmployee->net_payble           = $finalSalary - $monthlyTax;
                            $payslipEmployee->salary_month         = $formate_month_year;
                            $payslipEmployee->status               = 0;
                            $payslipEmployee->basic_salary         = !empty($employee->salary) ? $employee->salary : 0;
                            $payslipEmployee->allowance            = Employee::allowance($employee->id);
                            $payslipEmployee->commission           = Employee::commission($employee->id);
                            $payslipEmployee->loan                 = Employee::loan($employee->id);
                            $payslipEmployee->saturation_deduction = Employee::saturation_deduction($employee->id);
                            $payslipEmployee->other_payment        = Employee::other_payment($employee->id);
                            $payslipEmployee->overtime             = Employee::overtime($employee->id);
                            $payslipEmployee->provident_fund       = $own_provident_fund;
                            $payslipEmployee->tax                  = $monthlyTax;
                            $payslipEmployee->created_by           = \Auth::user()->creatorId();
                            $payslipEmployee->save();



                            //For Notification
                            $setting  = Utility::settings(\Auth::user()->creatorId());
                            $payslipNotificationArr = [
                                'year' =>  $formate_month_year,
                            ];
                            //Slack Notification
                            if(isset($setting['payslip_notification']) && $setting['payslip_notification'] ==1)
                            {
                                Utility::send_slack_msg('new_monthly_payslip', $payslipNotificationArr);
                            }

                            //Telegram Notification
                            if(isset($setting['telegram_payslip_notification']) && $setting['telegram_payslip_notification'] ==1)
                            {
                                Utility::send_telegram_msg('new_monthly_payslip', $payslipNotificationArr);
                            }
                            //webhook
                            $module ='New Monthly Payslip';
                            $webhook=  Utility::webhookSetting($module);
                            if($webhook)
                            {
                                $parameter = json_encode($payslipEmployee);
                                $status = Utility::WebhookCall($webhook['url'],$parameter,$webhook['method']);

                                if($status == true)
                                {
                                    return redirect()->back()->with('success', __('Payslip successfully created.'));
                                }
                                else
                                {
                                    return redirect()->back()->with('error', __('Webhook call failed.'));
                                }
                            }
                        }


                    }

                    return redirect()->route('payslip.index')->with('success', __('Payslip successfully created.'));
                }
                else
                {
                    return redirect()->route('payslip.index')->with('error', __('Payslip Already created.'));
                }*/

    }

    public function calculateTax($taxableIncome)
    {
        $taxSteps = [
            ['limit' => 350000, 'rate' => 0.00], // Up to 350,000 (the next 350,000 after 0)
            ['limit' => 450000, 'rate' => 0.05], // Up to 450,000 (the next 100,000 after 350,000)
            ['limit' => 750000, 'rate' => 0.10], // Up to 750,000 (the next 300,000 after 450,000)
            ['limit' => 1150000, 'rate' => 0.15], // Up to 1,150,000 (the next 400,000 after 750,000)
            ['limit' => 1650000, 'rate' => 0.20], // Up to 1,650,000 (the next 500,000 after 1,150,000)
            ['limit' => PHP_INT_MAX, 'rate' => 0.25] // Above 1,650,000
        ];

        $taxAmount = 0;
        $previousLimit = 0; // Start from zero

        foreach ($taxSteps as $index => $step) {
            // Determine the taxable amount for this step
            if ($taxableIncome > $step['limit']) {
                $taxableAtThisStep = $step['limit'] - $previousLimit;
            } else {
                $taxableAtThisStep = $taxableIncome - $previousLimit;
                // Apply the tax rate for this slab
                $taxAmount += $taxableAtThisStep * $step['rate'];
                break; // No more calculation needed as we have reached the taxable income
            }

            // Apply the tax rate for this slab
            $taxAmount += $taxableAtThisStep * $step['rate'];
            $previousLimit = $step['limit']; // Update the previous limit for the next iteration

            // If the taxable income is less than the current slab limit, no need to continue
            if ($taxableIncome < $step['limit']) {
                break;
            }
        }

        return $taxAmount;
    }

    public function destroy($id)
    {
        $payslip = PaySlip::find($id);
        $payslip->delete();

        return true;
    }

    public function showemployee($paySlip)
    {
        $payslip = PaySlip::find($paySlip);

        return view('payslip.show', compact('payslip'));
    }


    public function search_json(Request $request)
    {

        $formate_month_year = $request->datePicker;
        $validatePaysilp = PaySlip::where('salary_month', '=', $formate_month_year)->where('created_by', \Auth::user()->creatorId())->get()->toarray();

        $data = [];
        if (empty($validatePaysilp)) {
            $data = [];
            return;
        } else {
            $paylip_employee = PaySlip::select(
                [
                    'employees.id',
                    'employees.employee_id',
                    'employees.name',
                    'payslip_types.name as payroll_type',
                    'pay_slips.basic_salary',
                    'pay_slips.net_payble',
                    'pay_slips.id as pay_slip_id',
                    'pay_slips.status',
                    'pay_slips.approval',
                    'employees.user_id',
                ]
            )->leftjoin(
                    'employees',
                    function ($join) use ($formate_month_year) {
                        $join->on('employees.id', '=', 'pay_slips.employee_id');
                        $join->on('pay_slips.salary_month', '=', \DB::raw("'" . $formate_month_year . "'"));
                        $join->leftjoin('payslip_types', 'payslip_types.id', '=', 'employees.salary_type');
                    }
                )->where('employees.created_by', \Auth::user()->creatorId())->get();



            $module = 'payroll';

            $user_id = Auth::id();

            $roleId = DB::table('model_has_roles')->where('model_id', $user_id)->value('role_id');
            $isApproval = Approval::where('module', $module)->where('role_id', $roleId)->get();


            foreach ($paylip_employee as $employee) {
                $isAdmin = 0;
                $approvalAvailable = 0;
                $statusChecked = 0;

                if (!$isApproval->isEmpty()) {
                    $isAdmin = 1;
                    $order = $isApproval[0]->order;
                    $minOrder = Approval::where('module', $module)->min('order');

                    if ($order == $minOrder) {
                        $approvalAvailable = 1;
                    } else {
                        $prevRole = Approval::where('module', $module)->where('order', '<', $order)->orderBy('order', 'desc')->value('role_id');

                        $approvalArray = ApprovalStatus::where('module', $module)->where('module_id', $employee->pay_slip_id)
                            ->where('role_id', $prevRole)->where('status', 1)->get();

                        if (!$approvalArray->isEmpty()) {
                            $approvalAvailable = 1;
                        }
                    }

                    $approvalChecked = ApprovalStatus::where('module', $module)
                        ->where('module_id', $employee->pay_slip_id)
                        ->where('role_id', $roleId)->first();

                    if (isset($approvalChecked)) {
                        $statusChecked = 1;
                    }
                }

                if (Auth::user()->type == 'Employee') {
                    if (Auth::user()->id == $employee->user_id) {
                        $tmp = [];
                        $tmp[] = $employee->id;
                        $tmp[] = $employee->name;
                        $tmp[] = $employee->payroll_type;
                        $tmp[] = $employee->pay_slip_id;
                        $tmp[] = !empty($employee->basic_salary) ? \Auth::user()->priceFormat($employee->basic_salary) : '-';
                        $tmp[] = !empty($employee->net_payble) ? \Auth::user()->priceFormat($employee->net_payble) : '-';
                        if ($employee->status == 1) {
                            $tmp[] = 'paid';
                        } else {
                            $tmp[] = 'unpaid';
                        }
                        $tmp[] = !empty($employee->pay_slip_id) ? $employee->pay_slip_id : 0;
                        $tmp['url'] = route('employee.show', Crypt::encrypt($employee->id));
                        $tmp['approval'] = $employee->approval;
                        $tmp['isAdmin'] = $isAdmin;
                        $tmp['approvalAvailable'] = $approvalAvailable;
                        $tmp['statusChecked'] = $statusChecked;
                        $data[] = $tmp;

                    }
                } else {

                    $tmp = [];
                    $tmp[] = $employee->id;
                    $tmp[] = $employee->employee_id;
                    $tmp[] = $employee->name;
                    $tmp[] = $employee->payroll_type;
                    $tmp[] = !empty($employee->basic_salary) ? \Auth::user()->priceFormat($employee->basic_salary) : '-';
                    $tmp[] = !empty($employee->net_payble) ? \Auth::user()->priceFormat($employee->net_payble) : '-';
                    if ($employee->status == 1) {
                        $tmp[] = 'Paid';
                    } else {
                        $tmp[] = 'UnPaid';
                    }
                    $tmp[] = !empty($employee->pay_slip_id) ? $employee->pay_slip_id : 0;
                    $tmp['url'] = route('employee.show', Crypt::encrypt($employee->id));
                    $tmp['approval'] = $employee->approval;
                    $tmp['isAdmin'] = $isAdmin;
                    $tmp['approvalAvailable'] = $approvalAvailable;
                    $tmp['statusChecked'] = $statusChecked;
                    $data[] = $tmp;

                }
            }

            return $data;
        }
    }

    public function paysalary($id, $date)
    {
        $employeePayslip = PaySlip::where('employee_id', '=', $id)->where('created_by', \Auth::user()->creatorId())->where('approval', 'approved')->where('salary_month', '=', $date)->first();
        if (!empty($employeePayslip)) {
            $employeePayslip->status = 1;
            $employeePayslip->save();

            return redirect()->route('payslip.index')->with('success', __('Payslip Payment successfully.'));
        } else {
            return redirect()->route('payslip.index')->with('error', __('Payslip Payment failed.'));
        }

    }
    public function bulk_pay_create($date, $cash_val)
    {
        $Employees = PaySlip::where('salary_month', $date)->where('created_by', \Auth::user()->creatorId())->get();
        $unpaidEmployees = PaySlip::where('salary_month', $date)->where('created_by', \Auth::user()->creatorId())->where('status', '=', 0)->get();

        return view('payslip.bulkcreate', compact('Employees', 'unpaidEmployees', 'date', 'cash_val'));
    }

    public function bulkpayment(Request $request, $date)
    {
        $unpaidEmployees = PaySlip::where('salary_month', $date)->where('created_by', \Auth::user()->creatorId())->where('status', '=', 0)->get();

        foreach ($unpaidEmployees as $employee) {
            $employee->status = 1;
            $employee->save();
        }

        return redirect()->route('payslip.index')->with('success', __('Payslip Bulk Payment successfully.'));
    }

    public function employeepayslip()
    {
        $employees = Employee::where(
            [
                'user_id' => \Auth::user()->id,
            ]
        )->first();

        $payslip = PaySlip::where('employee_id', '=', $employees->id)->get();

        return view('payslip.employeepayslip', compact('payslip'));

    }

    public function pdf($id, $month)
    {

        $payslip = PaySlip::where('employee_id', $id)->where('salary_month', $month)->where('created_by', \Auth::user()->creatorId())->first();
        $employee = Employee::find($payslip->employee_id);

        // dd($employee);

        $payslipDetail = Utility::employeePayslipDetail($id, $month);

        $showBonus = false;
        if ((int) $employee->bonus > 0) {
            $showBonus = true;
        }

        return view('payslip.pdf', compact('payslip', 'employee', 'payslipDetail', 'showBonus'));
    }

    public function send($id, $month)
    {
        $setings = Utility::settings();
        //        dd($setings);
        if ($setings['payslip_sent'] == 1) {
            $payslip = PaySlip::where('employee_id', $id)->where('salary_month', $month)->where('created_by', \Auth::user()->creatorId())->first();
            $employee = Employee::find($payslip->employee_id);

            $payslip->name = $employee->name;
            $payslip->email = $employee->email;

            $payslipId = Crypt::encrypt($payslip->id);
            $payslip->url = route('payslip.payslipPdf', $payslipId);
            //            dd($payslip->url);

            $payslipArr = [

                'employee_name' => $employee->name,
                'employee_email' => $employee->email,
                'payslip_name' => $payslip->name,
                'payslip_salary_month' => $payslip->salary_month,
                'payslip_url' => $payslip->url,

            ];
            $resp = Utility::sendEmailTemplate('payslip_sent', [$employee->id => $employee->email], $payslipArr);



            return redirect()->back()->with('success', __('Payslip successfully sent.') . (($resp['is_success'] == false && !empty($resp['error'])) ? '<br> <span class="text-danger">' . $resp['error'] . '</span>' : ''));
        }

        return redirect()->back()->with('success', __('Payslip successfully sent.'));

    }

    public function payslipPdf($id)
    {
        $payslipId = Crypt::decrypt($id);

        $payslip = PaySlip::where('id', $payslipId)->where('created_by', \Auth::user()->creatorId())->first();
        $employee = Employee::find($payslip->employee_id);

        $payslipDetail = Utility::employeePayslipDetail($payslip->employee_id);

        $showBonus = false;
        if ((int) $employee->bonus > 0) {
            $showBonus = true;
        }

        return view('payslip.payslipPdf', compact('payslip', 'employee', 'payslipDetail', 'showBonus'));
    }

    public function editEmployee($paySlip)
    {
        $payslip = PaySlip::find($paySlip);
        $bonus = 0;
        if ($payslip && (int) $payslip->employee_id > 0) {
            $employee = Employee::find($payslip->employee_id);
            $bonus = $employee->bonus;
        }

        return view('payslip.salaryEdit', compact('payslip', 'bonus'));
    }

    public function updateEmployee(Request $request, $id)
    {


        if (isset($request->allowance) && !empty($request->allowance)) {
            $allowances = $request->allowance;
            $allowanceIds = $request->allowance_id;
            foreach ($allowances as $k => $allownace) {
                $allowanceData = Allowance::find($allowanceIds[$k]);
                $allowanceData->amount = $allownace;
                $allowanceData->save();
            }
        }


        if (isset($request->commission) && !empty($request->commission)) {
            $commissions = $request->commission;
            $commissionIds = $request->commission_id;
            foreach ($commissions as $k => $commission) {
                $commissionData = Commission::find($commissionIds[$k]);
                $commissionData->amount = $commission;
                $commissionData->save();
            }
        }

        if (isset($request->loan) && !empty($request->loan)) {
            $loans = $request->loan;
            $loanIds = $request->loan_id;
            foreach ($loans as $k => $loan) {
                $loanData = Loan::find($loanIds[$k]);
                $loanData->amount = $loan;
                $loanData->save();
            }
        }


        if (isset($request->saturation_deductions) && !empty($request->saturation_deductions)) {
            $saturation_deductionss = $request->saturation_deductions;
            $saturation_deductionsIds = $request->saturation_deductions_id;
            foreach ($saturation_deductionss as $k => $saturation_deductions) {

                $saturation_deductionsData = SaturationDeduction::find($saturation_deductionsIds[$k]);
                $saturation_deductionsData->amount = $saturation_deductions;
                $saturation_deductionsData->save();
            }
        }


        if (isset($request->other_payment) && !empty($request->other_payment)) {
            $other_payments = $request->other_payment;
            $other_paymentIds = $request->other_payment_id;
            foreach ($other_payments as $k => $other_payment) {
                $other_paymentData = OtherPayment::find($other_paymentIds[$k]);
                $other_paymentData->amount = $other_payment;
                $other_paymentData->save();
            }
        }


        if (isset($request->rate) && !empty($request->rate)) {
            $rates = $request->rate;
            $rateIds = $request->rate_id;
            $hourses = $request->hours;

            foreach ($rates as $k => $rate) {
                $overtime = Overtime::find($rateIds[$k]);
                $overtime->rate = $rate;
                $overtime->hours = $hourses[$k];
                $overtime->save();
            }
        }


        $payslipEmployee = PaySlip::find($request->payslip_id);
        $payslipEmployee->allowance = Employee::allowance($payslipEmployee->employee_id);
        $payslipEmployee->commission = Employee::commission($payslipEmployee->employee_id);
        $payslipEmployee->loan = Employee::loan($payslipEmployee->employee_id);
        $payslipEmployee->saturation_deduction = Employee::saturation_deduction($payslipEmployee->employee_id);
        $payslipEmployee->other_payment = Employee::other_payment($payslipEmployee->employee_id);
        $payslipEmployee->overtime = Employee::overtime($payslipEmployee->employee_id);
        $payslipEmployee->net_payble = Employee::find($payslipEmployee->employee_id)->get_net_salary();
        $payslipEmployee->save();

        return redirect()->route('payslip.index')->with('success', __('Employee payroll successfully updated.'));
    }

    public function export(Request $request)
    {
        $name = 'payslip_' . date('Y-m-d i:h:s');
        $data = Excel::download(new PayslipExport($request), $name . '.xlsx');
        ob_end_clean();
        return $data;
    }


    public function exportPDF(Request $request)
    {
        $name = 'payslip_' . date('Y-m-d_H-i-s') . '.pdf';

        // Prepare the data
        $payslipData = (new PayslipExport($request))->collection();

        return view('payslip.export-pdf', compact('payslipData'));
    }

    /**
     * @author Md. Salaquzzaman
     * @created-date 15-04-2024
     * @purpose Get API Access
     */
    public function getAPIAccess()
    {
        return $payload = [
            'api_token' => session()->get('api_token'),
            'company_no' => env('COMPANY_NUMBER'),
            'api_ip' => env('API_IP')
        ];
    }

    public function approvePayroll($id)
    {
        $userId = auth()->user()->id;
        $roleId = DB::table('model_has_roles')->where('model_id', $userId)->value('role_id');

        ApprovalStatus::create([
            "module" => "payroll",
            "module_id" => $id,
            "user_id" => $userId,
            "role_id" => $roleId,
            "status" => 1
        ]);

        $totalApproval = Approval::where('module', 'payroll')->count();

        $totalApproved = ApprovalStatus::where('module', 'payroll')->where('module_id', $id)->count();

        if ($totalApproval == $totalApproved) {
            PaySlip::where('id', $id)->update(['approval' => 'Approved']);
        }

        return redirect()->route('payslip.index')->with('success', __('Payroll Approved.'));
    }

    public function rejectPayroll($id)
    {
        $userId = auth()->user()->id;
        $roleId = DB::table('model_has_roles')->where('model_id', $userId)->value('role_id');

        ApprovalStatus::create([
            "module" => "payroll",
            "module_id" => $id,
            "user_id" => $userId,
            "role_id" => $roleId,
            "status" => 0
        ]);

        PaySlip::where('id', $id)->update(['approval' => 'Rejected']);

        return redirect()->route('payslip.index')->with('success', __('Payroll Rejected.'));
    }

}
