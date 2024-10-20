<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Role;
use App\Models\Approval;

class ApprovalSetup extends Controller
{
    public function index(){
        $roles = DB::table('roles')->get();
        $approvals = Approval::with('role')->get();
        return view('approvalSetup.approvalSetup', compact('roles', 'approvals'));
    }

    public function leaveApprovalStore(Request $request)
    {
        $request->validate([
            'role_id' => 'required',
        ]);

        $module = 'leave';

        $maxOrder = Approval::where('module', $module)->max('order');
        $order = ($maxOrder !== null) ? $maxOrder + 1 : 1;

        Approval::create([
            'module' => $module,
            'role_id' => $request->role_id,
            'order' => $order,
        ]);

        return redirect()->route('approval.setup')->with('success', __('Approval Role successfully created.'));
    }

    public function leaveApprovalDelete($id)
    {
        $approval = Approval::find($id);

        if (!$approval) {

            return redirect()->route('approval.setup')->with('error', 'Approval record not found.');
        }

        $approval->delete();

        return redirect()->route('approval.setup')->with('success', 'Approval Role deleted successfully.');
    }

    public function recruitmentApprovalStore(Request $request)
    {
        $request->validate([
            'role_id' => 'required',
        ]);

        $module = 'recruitment';

        $maxOrder = Approval::where('module', $module)->max('order');
        $order = ($maxOrder !== null) ? $maxOrder + 1 : 1;

        Approval::create([
            'module' => $module,
            'role_id' => $request->role_id,
            'order' => $order,
        ]);

        return redirect()->route('approval.setup')->with('success', __('Approval Role successfully created.'));
    }

    public function recruitmentApprovalDelete($id)
    {
        $approval = Approval::find($id);

        if (!$approval) {

            return redirect()->route('approval.setup')->with('error', 'Approval record not found.');
        }

        $approval->delete();

        return redirect()->route('approval.setup')->with('success', 'Approval Role deleted successfully.');
    }

    public function promotionApprovalStore(Request $request)
    {
        $request->validate([
            'role_id' => 'required',
        ]);

        $module = 'promotion';

        $maxOrder = Approval::where('module', $module)->max('order');
        $order = ($maxOrder !== null) ? $maxOrder + 1 : 1;

        Approval::create([
            'module' => $module,
            'role_id' => $request->role_id,
            'order' => $order,
        ]);

        return redirect()->route('approval.setup')->with('success', __('Approval Role successfully created.'));
    }

    public function promotionApprovalDelete($id)
    {
        $approval = Approval::find($id);

        if (!$approval) {

            return redirect()->route('approval.setup')->with('error', 'Approval record not found.');
        }

        $approval->delete();

        return redirect()->route('approval.setup')->with('success', 'Approval Role deleted successfully.');
    }

    public function terminationApprovalStore(Request $request)
    {
        $request->validate([
            'role_id' => 'required',
        ]);

        $module = 'termination';

        $maxOrder = Approval::where('module', $module)->max('order');
        $order = ($maxOrder !== null) ? $maxOrder + 1 : 1;

        Approval::create([
            'module' => $module,
            'role_id' => $request->role_id,
            'order' => $order,
        ]);

        return redirect()->route('approval.setup')->with('success', __('Approval Role successfully created.'));
    }

    public function terminationApprovalDelete($id)
    {
        $approval = Approval::find($id);

        if (!$approval) {

            return redirect()->route('approval.setup')->with('error', 'Approval record not found.');
        }

        $approval->delete();

        return redirect()->route('approval.setup')->with('success', 'Approval Role deleted successfully.');
    }

    public function payrollApprovalStore(Request $request)
    {
        $request->validate([
            'role_id' => 'required',
        ]);

        $module = 'payroll';

        $maxOrder = Approval::where('module', $module)->max('order');
        $order = ($maxOrder !== null) ? $maxOrder + 1 : 1;

        Approval::create([
            'module' => $module,
            'role_id' => $request->role_id,
            'order' => $order,
        ]);

        return redirect()->route('approval.setup')->with('success', __('Approval Role successfully created.'));
    }

    public function payrollApprovalDelete($id)
    {
        $approval = Approval::find($id);

        if (!$approval) {

            return redirect()->route('approval.setup')->with('error', 'Approval record not found.');
        }

        $approval->delete();

        return redirect()->route('approval.setup')->with('success', 'Approval Role deleted successfully.');
    }

}
