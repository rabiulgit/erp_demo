<?php

namespace App\Http\Controllers;

use App\Models\BillProduct;
use App\Models\InvoiceProduct;
use App\Models\ProposalProduct;
use App\Models\Vat;
use Auth;
use Illuminate\Http\Request;

class VatController extends Controller
{


    public function index()
    {
        // if(\Auth::user()->can('manage constant vat'))
        // {
            $vats = Vat::where('created_by', '=', \Auth::user()->creatorId())->get();

            return view('vats.index')->with('vats', $vats);
        // }
        // else
        // {
        //     return redirect()->back()->with('error', __('Permission denied.'));
        // }
    }


    public function create()
    {
        // if(\Auth::user()->can('create constant vat'))
        // {
            return view('vats.create');
        // }
        // else
        // {
        //     return response()->json(['error' => __('Permission denied.')], 401);
        // }
    }

    public function store(Request $request)
    {
        // if(\Auth::user()->can('create constant vat'))
        // {
            $validator = \Validator::make(
                $request->all(), [
                                   'name' => 'required|max:20',
                                   'rate' => 'required|numeric',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $vat             = new Vat();
            $vat->name       = $request->name;
            $vat->rate       = $request->rate;
            $vat->created_by = \Auth::user()->creatorId();
            $vat->save();

            return redirect()->route('vats.index')->with('success', __('Vat rate successfully created.'));
        // }
        // else
        // {
        //     return redirect()->back()->with('error', __('Permission denied.'));
        // }
    }

    public function show(Vat $vat)
    {
        return redirect()->route('vats.index');
    }


    public function edit(Vat $vat)
    {
        // if(\Auth::user()->can('edit constant vat'))
        // {
            if($vat->created_by == \Auth::user()->creatorId())
            {
                return view('vats.edit', compact('vat'));
            }
            else
            {
                return response()->json(['error' => __('Permission denied.')], 401);
            }
        // }
        // else
        // {
        //     return response()->json(['error' => __('Permission denied.')], 401);
        // }
    }


    public function update(Request $request, Vat $vat)
    {
        // if(\Auth::user()->can('edit constant vat'))
        // {
            if($vat->created_by == \Auth::user()->creatorId())
            {
                $validator = \Validator::make(
                    $request->all(), [
                                       'name' => 'required|max:20',
                                       'rate' => 'required|numeric',
                                   ]
                );
                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }

                $vat->name = $request->name;
                $vat->rate = $request->rate;
                $vat->save();

                return redirect()->route('vats.index')->with('success', __('Vat rate successfully updated.'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        // }
        // else
        // {
        //     return redirect()->back()->with('error', __('Permission denied.'));
        // }
    }

    public function destroy(Vat $vat)
    {
        // if(\Auth::user()->can('delete constant vat'))
        // {
            if($vat->created_by == \Auth::user()->creatorId())
            {
                $proposalData = ProposalProduct::whereRaw("find_in_set('$vat->id',vat)")->first();
                $billData     = BillProduct::whereRaw("find_in_set('$vat->id',vat)")->first();
                $invoiceData  = InvoiceProduct::whereRaw("find_in_set('$vat->id',vat)")->first();

                if(!empty($proposalData) || !empty($billData) || !empty($invoiceData))
                {
                    return redirect()->back()->with('error', __('this vat is already assign to proposal or bill or invoice so please move or remove this vat related data.'));
                }

                $vat->delete();

                return redirect()->route('vats.index')->with('success', __('Vat rate successfully deleted.'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        // }
        // else
        // {
        //     return redirect()->back()->with('error', __('Permission denied.'));
        // }
    }
}
