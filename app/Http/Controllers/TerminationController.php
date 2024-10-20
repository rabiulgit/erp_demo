<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Termination;
use App\Models\TerminationType;
use App\Models\Utility;
use App\Models\Approval;
use App\Models\ApprovalStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class TerminationController extends Controller
{
    public function index()
    {
        if(\Auth::user()->can('manage termination'))
        {
            $isAdmin=0;
            $module='termination';

            $user_id = Auth::id();

            $roleId = DB::table('model_has_roles')->where('model_id', $user_id)->value('role_id');
            $isApproval = Approval::where('module', $module)->where('role_id', $roleId)->get();

            if ($isApproval->isEmpty()) {
                if(\Auth::user()->type == 'company'){
                    $terminations = Termination::where('created_by', '=', \Auth::user()->creatorId())->with(['terminationType','employee'])->get();
                }
                else{
                    $emp          = Employee::where('user_id', '=', \Auth::user()->id)->first();
                    $terminations = Termination::where('created_by', '=', \Auth::user()->creatorId())->where('employee_id', '=', $emp->id)->with(['terminationType','employee'])->get();
                }

            } else {
                $isAdmin=1;
                $order = $isApproval[0]->order;
                $minOrder = Approval::where('module', $module)->min('order');
                if ($order == $minOrder) {
                    $terminations = Termination::where('created_by', '=', \Auth::user()->creatorId())->with(['terminationType','employee'])->get();
                } else {
                    $terminations=[];
                    $prevRole = Approval::where('module', $module)->where('order', '<', $order)->orderBy('order', 'desc')->value('role_id');
    
                    $allTerminations = Termination::where('created_by', '=', \Auth::user()->creatorId())->with(['terminationType','employee'])->get();

                    foreach ($allTerminations as $termination) {
                        $approvalArray=ApprovalStatus::where('module', $module)->where('module_id', $termination->id)
                            ->where('role_id', $prevRole)->where('status', 1)->get();

                        if(!$approvalArray->isEmpty()){
                            $terminations[]=$termination;
                        }
                        
                    }
                }
            }
    
            foreach ($terminations as $termination) {

                if(!$isApproval->isEmpty()){
                    $statusChecked=ApprovalStatus::where('module', $module)
                        ->where('module_id','=',$termination->id)
                        ->where('role_id','=',$roleId)->first();
    
                    if(isset($statusChecked)){
                        $termination->statusChecked=1;
                        $termination->leaveStatus=$statusChecked->status;
                    }
                    else{
                        $termination->statusChecked=0;
                    }
                }
            }


            return view('termination.index', compact('terminations','isAdmin'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        if(\Auth::user()->can('create termination'))
        {
            $employees        = Employee::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $terminationtypes = TerminationType::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');

            return view('termination.create', compact('employees', 'terminationtypes'));
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function store(Request $request)
    {
        if(\Auth::user()->can('create termination'))
        {

            $validator = \Validator::make(
                $request->all(), [
                                   'employee_id' => 'required',
                                   'termination_type' => 'required',
                                   'notice_date' => 'required',
                                   'termination_date' => 'required',
                               ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $termination                   = new Termination();
            $termination->employee_id      = $request->employee_id;
            $termination->termination_type = $request->termination_type;
            $termination->notice_date      = $request->notice_date;
            $termination->termination_date = $request->termination_date;
            $termination->description      = $request->description;
            $termination->status           = 'Pending';
            $termination->created_by       = \Auth::user()->creatorId();
            $termination->save();

            $setings = Utility::settings();
            if($setings['termination_sent'] == 1)
            {
                $employee           = Employee::find($termination->employee_id);
//                $termination->name  = $employee->name;
//                $termination->email = $employee->email;
                $termination->type  = TerminationType::find($termination->termination_type);

                $terminationArr = [
                    'termination_name'=>$employee->name,
                    'termination_email'=>$employee->email,
                    'notice_date'=>$termination->notice_date,
                    'termination_date'=>$termination->termination_date,
                    'termination_type'=>$request->termination_type,
                ];

                $resp = Utility::sendEmailTemplate('termination_sent', [$employee->id => $employee->email], $terminationArr);


                return redirect()->route('termination.index')->with('success', __('Termination  successfully created.') .(($resp['is_success'] == false && !empty($resp['error'])) ? '<br> <span class="text-danger">' . $resp['error'] . '</span>' : ''));

            }

            return redirect()->route('termination.index')->with('success', __('Termination  successfully created.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function show(Termination $termination)
    {
        return redirect()->route('termination.index');
    }

    public function edit(Termination $termination)
    {
        if(\Auth::user()->can('edit termination'))
        {
            $employees        = Employee::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $terminationtypes = TerminationType::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            if($termination->created_by == \Auth::user()->creatorId())
            {

                return view('termination.edit', compact('termination', 'employees', 'terminationtypes'));
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

    public function update(Request $request, Termination $termination)
    {
        if(\Auth::user()->can('edit termination'))
        {
            if($termination->created_by == \Auth::user()->creatorId())
            {
                $validator = \Validator::make(
                    $request->all(), [
                                       'employee_id' => 'required',
                                       'termination_type' => 'required',
                                       'notice_date' => 'required',
                                       'termination_date' => 'required',
                                   ]
                );

                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }


                $termination->employee_id      = $request->employee_id;
                $termination->termination_type = $request->termination_type;
                $termination->notice_date      = $request->notice_date;
                $termination->termination_date = $request->termination_date;
                $termination->description      = $request->description;
                $termination->save();

                return redirect()->route('termination.index')->with('success', __('Termination successfully updated.'));
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

    public function destroy(Termination $termination)
    {
        if(\Auth::user()->can('delete termination'))
        {
            if($termination->created_by == \Auth::user()->creatorId())
            {
                $termination->delete();

                return redirect()->route('termination.index')->with('success', __('Termination successfully deleted.'));
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

    public function description($id)
    {
        $termination = Termination::find($id);

        return view('termination.description', compact('termination'));
    }

    public function approveTermination($id)
    {
        $userId=auth()->user()->id;
        $roleId = DB::table('model_has_roles')->where('model_id', $userId)->value('role_id');

        ApprovalStatus::create([
            "module" => "termination",
            "module_id" => $id,
            "user_id" => $userId,
            "role_id" => $roleId,
            "status" => 1
        ]);

        $totalApproval = Approval::where('module', 'termination')->count();

        $totalApproved = ApprovalStatus::where('module', 'termination')->where('module_id', $id)->count();

        if($totalApproval == $totalApproved){
            Termination::where('id', $id)->update(['status' => 'Approved']);
        }

        return redirect()->route('termination.index')->with('success', __('Termination Approved.'));
    }

    public function rejectTermination($id)
    {
        $userId=auth()->user()->id;
        $roleId = DB::table('model_has_roles')->where('model_id', $userId)->value('role_id');

        ApprovalStatus::create([
            "module" => "termination",
            "module_id" => $id,
            "user_id" => $userId,
            "role_id" => $roleId,
            "status" => 0
        ]);

        Termination::where('id', $id)->update(['status' => 'Rejected']);

        return redirect()->route('termination.index')->with('success', __('Termination Rejected.'));
    }

}
