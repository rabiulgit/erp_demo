<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Leave;
use App\Models\LeaveType;
use App\Models\Mail\LeaveActionSend;
use App\Models\Utility;
use App\Models\Approval;
use App\Models\ApprovalStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class LeaveController extends Controller
{
    public function index()
    {
        if(\Auth::user()->can('manage leave'))
        {
            $isAdmin=0;
            $module='leave';

            $user_id = Auth::id();

            $roleId = DB::table('model_has_roles')->where('model_id', $user_id)->value('role_id');
            $isApproval = Approval::where('module', $module)->where('role_id', $roleId)->get();
    
            if ($isApproval->isEmpty()) {

                if(\Auth::user()->type == 'company'){
                    $leaves = Leave::where('created_by', \Auth::user()->creatorId())->with(['employees','leaveType'])->get();
                }
                else{
                    $employee_id =Employee::where('user_id', $user_id)->value('id');
                    $leaves = Leave::where('employee_id', $employee_id)->with(['employees','leaveType'])->get();
                }

            } else {
                $isAdmin=1;
                $order = $isApproval[0]->order;
                $minOrder = Approval::where('module', $module)->min('order');
                if ($order == $minOrder) {
                    $leaves = Leave::with(['employees','leaveType'])->get();
                } else {
                    $leaves=[];
                    $prevRole = Approval::where('module', $module)->where('order', '<', $order)->orderBy('order', 'desc')->value('role_id');
    
                    $allLeaves = Leave::with(['employees','leaveType'])->get();
    
                    $employee_id =Employee::where('user_id', $user_id)->value('id');

                    foreach ($allLeaves as $leave) {
                        if($leave->employee_id == $employee_id){
                            $leaves[]=$leave;
                        }
                        else{
                            $approvalArray=ApprovalStatus::where('module', $module)->where('module_id', $leave->id)
                            ->where('role_id', $prevRole)->where('status', 1)->get();

                            if(!$approvalArray->isEmpty()){
                                $leaves[]=$leave;
                            }
                        }
                        
                    }
                }
            }
            
            $minOrderRole = Approval::where('module', $module)
            ->where('order', Approval::where('module', $module)->min('order'))
            ->value('role_id');
    
            foreach ($leaves as $leave) {
                $approveStatus=ApprovalStatus::where('module', $module)
                        ->where('module_id', $leave->id)
                        ->where('role_id', $minOrderRole)->get();

                if($approveStatus->isEmpty()){
                    $leave->isChecked=0;
                }
                else{
                    $leave->isChecked=1;
                }

                if(!$isApproval->isEmpty()){
                    $statusChecked=ApprovalStatus::where('module', $module)
                        ->where('module_id','=',$leave->id)
                        ->where('role_id','=',$roleId)->first();
    
                    if(isset($statusChecked)){
                        $leave->statusChecked=1;
                        $leave->leaveStatus=$statusChecked->status;
                    }
                    else{
                        $leave->statusChecked=0;
                    }
                }
            }
            

            return view('leave.index', compact('leaves','isAdmin'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        if(\Auth::user()->can('create leave'))
        {
            if(Auth::user()->type == 'Employee')
            {
                $employees = Employee::where('user_id', '=', \Auth::user()->id)->get()->pluck('name', 'id');
            }
            else
            {
                $employees = Employee::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            }
            $leavetypes      = LeaveType::where('created_by', '=', \Auth::user()->creatorId())->get();

            return view('leave.create', compact('employees', 'leavetypes'));
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function store(Request $request)
    {
        if(\Auth::user()->can('create leave'))
        {
            $validator = \Validator::make(
                $request->all(), [
                    'leave_type_id' => 'required',
                    'start_date' => 'required',
                    'end_date' => 'required',
                    'leave_reason' => 'required',
                ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $employee = Employee::where('user_id', '=', Auth::user()->id)->first();
            $leave_type = LeaveType::find($request->leave_type_id);
            $startDate = new \DateTime($request->start_date);
            $endDate = new \DateTime($request->end_date);
            $endDate->add(new \DateInterval('P1D'));
            $total_leave_days = !empty($startDate->diff($endDate)) ? $startDate->diff($endDate)->days : 0;
            if ($leave_type->days >= $total_leave_days)
            {
                $leave    = new Leave();
                if(\Auth::user()->type == "Employee")
                {
                    $leave->employee_id = $employee->id;
                }
                else
                {
                    $leave->employee_id = $request->employee_id;
                }
                $leave->leave_type_id    = $request->leave_type_id;
                $leave->applied_on       = date('Y-m-d');
                $leave->start_date       = $request->start_date;
                $leave->end_date         = $request->end_date;
                $leave->total_leave_days = $total_leave_days;
                $leave->leave_reason     = $request->leave_reason;
                $leave->remark           = !empty($request->remark) ? $request->remark : '';
                $leave->status           = 'Pending';
                $leave->created_by       = \Auth::user()->creatorId();

                if ($request->hasFile('attachment')) {
                    $filenameWithExt = $request->file('attachment')->getClientOriginalName();
                    $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                    $extension = $request->file('attachment')->getClientOriginalExtension();
                    $fileNameToStore = $filename.'_'.time().'.'.$extension;
                    $path = $request->file('attachment')->storeAs('leave', $fileNameToStore);
        
                    $leave->attachment = $fileNameToStore;
                }

                $leave->save();

                return redirect()->route('leave.index')->with('success', __('Leave successfully created.'));
            } else {
                return redirect()->back()->with('error', __('Leave type ' . $leave_type->name . ' is provide maximum ' . $leave_type->days . "  days please make sure your selected days is under " . $leave_type->days . ' days.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function show(Leave $leave)
    {
        return redirect()->route('leave.index');
    }

    public function edit(Leave $leave)
    {
        if(\Auth::user()->can('edit leave'))
        {
            if($leave->created_by == \Auth::user()->creatorId())
            {
                $employees  = Employee::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
                $leavetypes = LeaveType::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('title', 'id');

                return view('leave.edit', compact('leave', 'employees', 'leavetypes'));
            }
            else
            {
                return response()->json(['error' => __('Permission denied.')], 401);
            }
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function update(Request $request, $leave)
    {

        $leave = Leave::find($leave);
        if(\Auth::user()->can('edit leave'))
        {
            if($leave->created_by == Auth::user()->creatorId())
            {
                $validator = \Validator::make(
                    $request->all(), [
                        'leave_type_id' => 'required',
                        'start_date' => 'required',
                        'end_date' => 'required',
                        'leave_reason' => 'required',
                    ]
                );
                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }

                $leave_type = LeaveType::find($request->leave_type_id);

                $startDate = new \DateTime($request->start_date);
                $endDate = new \DateTime($request->end_date);
                $endDate->add(new \DateInterval('P1D'));
                $total_leave_days = !empty($startDate->diff($endDate)) ? $startDate->diff($endDate)->days : 0;

                if ($leave_type->days >= $total_leave_days)
                {
                    $leave->employee_id      = $request->employee_id;
                    $leave->leave_type_id    = $request->leave_type_id;
                    $leave->start_date       = $request->start_date;
                    $leave->end_date         = $request->end_date;
                    $leave->total_leave_days = $total_leave_days;
                    $leave->leave_reason     = $request->leave_reason;
                    $leave->remark           = !empty($request->remark) ? $request->remark : '';

                    if ($request->hasFile('attachment')) {
                        
                        if(!empty($leave->attachment)){
                            Storage::delete('leave/' . $leave->attachment);
                        }
        
                        $filenameWithExt = $request->file('attachment')->getClientOriginalName();
                        $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                        $extension = $request->file('attachment')->getClientOriginalExtension();
                        $fileNameToStore = $filename.'_'.time().'.'.$extension;
                        $path = $request->file('attachment')->storeAs('leave', $fileNameToStore);
        
                        $leave->attachment = $fileNameToStore;
                    }

                    $leave->save();

                    return redirect()->route('leave.index')->with('success', __('Leave successfully updated.'));
                }
                else
                {
                    return redirect()->back()->with('error', __('Leave type ' . $leave_type->name . ' is provide maximum ' . $leave_type->days . "  days please make sure your selected days is under " . $leave_type->days . ' days.'));
                }
            }
            else
            {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy(Leave $leave)
    {
        if(\Auth::user()->can('delete leave'))
        {
            if($leave->created_by == \Auth::user()->creatorId())
            {
                $leave->delete();

                return redirect()->route('leave.index')->with('success', __('Leave successfully deleted.'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function action($id)
    {
        $leave     = Leave::find($id);
        $employee  = Employee::find($leave->employee_id);
        $leavetype = LeaveType::find($leave->leave_type_id);

        return view('leave.action', compact('employee', 'leavetype', 'leave'));
    }

    public function changeaction(Request $request)
    {

        $leave = Leave::find($request->leave_id);

        $leave->status = $request->status;
        if($leave->status == 'Approval')
        {
            $startDate               = new \DateTime($leave->start_date);
            $endDate                 = new \DateTime($leave->end_date);
            $total_leave_days        = $startDate->diff($endDate)->days;
            $leave->total_leave_days = $total_leave_days;
            $leave->status           = 'Approved';
        }

        $leave->save();

        //Send Email
        $setings = Utility::settings();
        if(!empty($employee->id))
        {
            if($setings['leave_status'] == 1)
            {

                $employee     = Employee::where('id', $leave->employee_id)->where('created_by', '=', \Auth::user()->creatorId())->first();
                $leave->name  = !empty($employee->name) ? $employee->name : '';
                $leave->email = !empty($employee->email) ? $employee->email : '';

                $actionArr = [

                    'leave_name'=> !empty($employee->name) ? $employee->name : '',
                    'leave_status' => $leave->status,
                    'leave_reason' =>  $leave->leave_reason,
                    'leave_start_date' => $leave->start_date,
                    'leave_end_date' => $leave->end_date,
                    'total_leave_days' => $leave->total_leave_days,

                ];
                $resp = Utility::sendEmailTemplate('leave_action_sent', [$employee->id => $employee->email], $actionArr);

                return redirect()->route('leave.index')->with('success', __('Leave status successfully updated.') .(($resp['is_success'] == false && !empty($resp['error'])) ? '<br> <span class="text-danger">' . $resp['error'] . '</span>' : ''));

            }

        }

        return redirect()->route('leave.index')->with('success', __('Leave status successfully updated.'));
    }

    public function jsoncount(Request $request)
    {
        $leave_counts=[];
        $leave_types = LeaveType::where('created_by',\Auth::user()->creatorId())->get();
        foreach ($leave_types as  $type) {
            $counts=Leave::select(\DB::raw('COALESCE(SUM(leaves.total_leave_days),0) AS total_leave'))->where('leave_type_id',$type->id)->groupBy('leaves.leave_type_id')->where('employee_id',$request->employee_id)->first();

            $leave_count['total_leave']=!empty($counts)?$counts['total_leave']:0;
            $leave_count['title']=$type->title;
            $leave_count['days']=$type->days;
            $leave_count['id']=$type->id;
            $leave_counts[]=$leave_count;
        }

        return $leave_counts;

    }

    public function approveLeave($id)
    {
        $userId=auth()->user()->id;
        $roleId = DB::table('model_has_roles')->where('model_id', $userId)->value('role_id');

        ApprovalStatus::create([
            "module" => "leave",
            "module_id" => $id,
            "user_id" => $userId,
            "role_id" => $roleId,
            "status" => 1
        ]);

        $totalApproval = Approval::where('module', 'leave')->count();

        $totalApproved = ApprovalStatus::where('module', 'leave')->where('module_id', $id)->count();

        if($totalApproval == $totalApproved){
            Leave::where('id', $id)->update(['status' => 'Approved']);
        }

        return redirect()->route('leave.index')->with('success', __('Leave Approved.'));
    }

    public function rejectLeave($id)
    {
        $userId=auth()->user()->id;
        $roleId = DB::table('model_has_roles')->where('model_id', $userId)->value('role_id');

        ApprovalStatus::create([
            "module" => "leave",
            "module_id" => $id,
            "user_id" => $userId,
            "role_id" => $roleId,
            "status" => 0
        ]);

        Leave::where('id', $id)->update(['status' => 'Rejected']);

        return redirect()->route('leave.index')->with('success', __('Leave Rejected.'));
    }

}
