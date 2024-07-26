<?php

namespace App\Http\Controllers;

use App\Models\Deviation;
use App\Models\extension_new;
use App\Models\extension_new_audit_trail;
use App\Models\ExtensionAuditTrail;
use App\Models\ExtensionNewAuditTrail;
use App\Models\RecordNumber;
use App\Models\RoleGroup;
use App\Models\User;
use Helpers;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use PDF;

class ExtensionNewController extends Controller
{

    public function index(Request $request){
        $data = "test";
        $record_numbers = (RecordNumber::first()->value('counter')) + 1;
        $record_number = str_pad($record_numbers, 4, '0', STR_PAD_LEFT);
        $reviewers = DB::table('user_roles')
                ->join('users', 'user_roles.user_id', '=', 'users.id')
                ->select('user_roles.q_m_s_processes_id', 'users.id','users.role','users.name') // Include all selected columns in the select statement
                ->where('user_roles.q_m_s_processes_id', 89)
                ->where('user_roles.q_m_s_roles_id', 2)
                ->groupBy('user_roles.q_m_s_processes_id', 'users.id','users.role','users.name') // Include all selected columns in the group by clause
                ->get();
        $approvers = DB::table('user_roles')
                ->join('users', 'user_roles.user_id', '=', 'users.id')
                ->select('user_roles.q_m_s_processes_id', 'users.id','users.role','users.name') // Include all selected columns in the select statement
                ->where('user_roles.q_m_s_processes_id', 89)
                ->where('user_roles.q_m_s_roles_id', 1)
                ->groupBy('user_roles.q_m_s_processes_id', 'users.id','users.role','users.name') // Include all selected columns in the group by clause
                ->get();


        return View('frontend.extension.extension_new', compact('data', 'reviewers', 'approvers', 'record_number'));
    }
    public function store(Request $request)
    {
        $request->validate([
        //     'site_location_code' => 'required|string',
        //     'initiator' => 'required|string',
        //     'initiation_date' => 'required|date',
            'short_description' => 'required|string',
        //     'reviewers' => 'required|json',
        //     'approvers' => 'required|json',
        //     'current_due_date' => 'required|date',
        //     'proposed_due_date' => 'required|date',
            // 'description' => 'required|string',
        //     'file_attachment_extension' => 'required|string',
        //     'reviewer_remarks' => 'nullable|string',
        //     'file_attachment_reviewer' => 'nullable|string',
        //     'approver_remarks' => 'nullable|string',
        //     'file_attachment_approver' => 'nullable|string',
        ]);
    
        $extensionNew = new extension_new();
        $extensionNew->type = "Extension";
        $extensionNew->stage = "1";
        $extensionNew->status = "Opened";

        $extensionNew->record_number = DB::table('record_numbers')->value('counter') + 1;
//   dd($extensionNew->record_number);
        $extensionNew->site_location_code = $request->site_location_code;
        // dd($request->site_location_code);
        $extensionNew->initiator = Auth::user()->id;

        // dd($request->initiator);
        $extensionNew->parent_id = $request->parent_id;
        $extensionNew->parent_type = $request->parent_type;
        $extensionNew->parent_record = $request->parent_record;

        $extensionNew->initiation_date = $request->initiation_date;
        $extensionNew->short_description = $request->short_description;
        $extensionNew->reviewers = $request->reviewers;
        $extensionNew->approvers = $request->approvers;
        $extensionNew->current_due_date = $request->current_due_date;
        $extensionNew->proposed_due_date = $request->proposed_due_date;
        $extensionNew->description = $request->description;
        $extensionNew->justification_reason = $request->justification_reason;
        $extensionNew->file_attachment_extension = $request->file_attachment_extension;
        $extensionNew->reviewer_remarks = $request->reviewer_remarks;
        $extensionNew->file_attachment_reviewer = $request->file_attachment_reviewer;
        $extensionNew->approver_remarks = $request->approver_remarks;
        $extensionNew->file_attachment_approver = $request->file_attachment_approver;
    
        $counter = DB::table('record_numbers')->value('counter');
        // Generate the record number with leading zeros
        $record_number = str_pad($counter, 5, '0', STR_PAD_LEFT);
        // Increment the counter value
        $newCounter = $counter + 1;
        DB::table('record_numbers')->update(['counter' => $newCounter]);
        
        if (!empty ($request->file_attachment_extension)) {
            $files = [];
            if ($request->hasfile('file_attachment_extension')) {
                foreach ($request->file('file_attachment_extension') as $file) {
                    $name = $request->name . 'file_attachment_extension' . rand(1, 100) . '.' . $file->getClientOriginalExtension();
                    $file->move('upload/', $name);
                    $files[] = $name;
                }
            }


            $extensionNew->file_attachment_extension = json_encode($files);
        }

        if (!empty ($request->file_attachment_reviewer)) {
            $files = [];
            if ($request->hasfile('file_attachment_reviewer')) {
                foreach ($request->file('file_attachment_reviewer') as $file) {
                    $name = $request->name . 'file_attachment_reviewer' . rand(1, 100) . '.' . $file->getClientOriginalExtension();
                    $file->move('upload/', $name);
                    $files[] = $name;
                }
            }


            $extensionNew->file_attachment_reviewer = json_encode($files);
        }

        if (!empty ($request->file_attachment_approver)) {
            $files = [];
            if ($request->hasfile('file_attachment_approver')) {
                foreach ($request->file('file_attachment_approver') as $file) {
                    $name = $request->name . 'file_attachment_approver' . rand(1, 100) . '.' . $file->getClientOriginalExtension();
                    $file->move('upload/', $name);
                    $files[] = $name;
                }
            }


            $extensionNew->file_attachment_approver = json_encode($files);
        }

        // $record = RecordNumber::first();
        // $record->counter = ((RecordNumber::first()->value('counter')) + 1);
        // $record->update();


        $extensionNew->save();
        if (!empty ($request->reviewers)){
            $history = new ExtensionNewAuditTrail();
            $history->extension_id = $extensionNew->id;
            $history->activity_type = 'HOD Reviewer';
            $history->previous = "Null";
            $history->current = $extensionNew->reviewers;
            $history->comment = "Not Applicable";
            $history->user_id = Auth::user()->id;
            $history->user_name = Auth::user()->name;
            $history->user_role = RoleGroup::where('id', Auth::user()->role)->value('name');
            $history->origin_state = $extensionNew->status;
            $history->change_to =   "Opened";
            $history->change_from = "Initiation";
            $history->action_name = 'Create';
            $history->save();
        }

        if (!empty ($request->approver_remarks)){
            $history = new ExtensionNewAuditTrail();
            $history->extension_id = $extensionNew->id;
            $history->activity_type = 'QA Remarks';
            $history->previous = "Null";
            $history->current = $extensionNew->approver_remarks;
            $history->comment = "Not Applicable";
            $history->user_id = Auth::user()->id;
            $history->user_name = Auth::user()->name;
            $history->user_role = RoleGroup::where('id', Auth::user()->role)->value('name');
            $history->origin_state = $extensionNew->status;
            $history->change_to =   "Opened";
            $history->change_from = "Initiation";
            $history->action_name = 'Create';
            $history->save();
        }

        if (!empty ($request->reviewer_remarks)){
            $history = new ExtensionNewAuditTrail();
            $history->extension_id = $extensionNew->id;
            $history->activity_type = 'HOD Remarks';
            $history->previous = "Null";
            $history->current = $extensionNew->reviewer_remarks;
            $history->comment = "Not Applicable";
            $history->user_id = Auth::user()->id;
            $history->user_name = Auth::user()->name;
            $history->user_role = RoleGroup::where('id', Auth::user()->role)->value('name');
            $history->origin_state = $extensionNew->status;
            $history->change_to =   "Opened";
            $history->change_from = "Initiation";
            $history->action_name = 'Create';
            $history->save();
        }

        if (!empty ($request->approvers)){
            $history = new ExtensionNewAuditTrail();
            $history->extension_id = $extensionNew->id;
            $history->activity_type = 'QA Approver';
            $history->previous = "Null";
            $history->current = $extensionNew->approvers;
            $history->comment = "Not Applicable";
            $history->user_id = Auth::user()->id;
            $history->user_name = Auth::user()->name;
            $history->user_role = RoleGroup::where('id', Auth::user()->role)->value('name');
            $history->origin_state = $extensionNew->status;
            $history->change_to =   "Opened";
            $history->change_from = "Initiation";
            $history->action_name = 'Create';
            $history->save();
        }

        if (!empty ($request->initiation_date)){
            $history = new ExtensionNewAuditTrail();
            $history->extension_id = $extensionNew->id;
            $history->activity_type = 'Initiation Date';
            $history->previous = "Null";
            $history->current =  Helpers::getDateFormat($extensionNew->initiation_date);
            $history->comment = "Not Applicable";
            $history->user_id = Auth::user()->id;
            $history->user_name = Auth::user()->name;
            $history->user_role = RoleGroup::where('id', Auth::user()->role)->value('name');
            $history->origin_state = $extensionNew->status;
            $history->change_to =   "Opened";
            $history->change_from = "Initiation";
            $history->action_name = 'Create';
            $history->save();
        }

        if (!empty ($request->site_location_code)){
            $history = new ExtensionNewAuditTrail();
            $history->extension_id = $extensionNew->id;
            $history->activity_type = 'Site Location Code';
            $history->previous = "Null";
            $history->current = Helpers::getDivisionName($extensionNew->site_location_code);
            $history->comment = "Not Applicable";
            $history->user_id = Auth::user()->id;
            $history->user_name = Auth::user()->name;
            $history->user_role = RoleGroup::where('id', Auth::user()->role)->value('name');
            $history->origin_state = $extensionNew->status;
            $history->change_to =   "Opened";
            $history->change_from = "Initiation";
            $history->action_name = 'Create';
            $history->save();
        }

        if (!empty ($request->initiator)){
            $history = new ExtensionNewAuditTrail();
            $history->extension_id = $extensionNew->id;
            $history->activity_type = 'Initiator';
            $history->previous = "Null";
            $history->current = $extensionNew->initiator;
            $history->comment = "Not Applicable";
            $history->user_id = Auth::user()->id;
            $history->user_name = Auth::user()->name;
            $history->user_role = RoleGroup::where('id', Auth::user()->role)->value('name');
            $history->origin_state = $extensionNew->status;
            $history->change_to =   "Opened";
            $history->change_from = "Initiation";
            $history->action_name = 'Create';
            $history->save();
        }
        if (!empty ($request->short_description)){
            $history = new ExtensionNewAuditTrail();
            $history->extension_id = $extensionNew->id;
            $history->activity_type = 'Short Description';
            $history->previous = "Null";
            $history->current = $extensionNew->short_description;
            $history->comment = "Not Applicable";
            $history->user_id = Auth::user()->id;
            $history->user_name = Auth::user()->name;
            $history->user_role = RoleGroup::where('id', Auth::user()->role)->value('name');
            $history->origin_state = $extensionNew->status;
            $history->change_to =   "Opened";
            $history->change_from = "Initiation";
            $history->action_name = 'Create';
            $history->save();
        }
        if (!empty ($request->current_due_date)){
            $history = new ExtensionNewAuditTrail();
            $history->extension_id = $extensionNew->id;
            $history->activity_type = 'Current Due Date (Parent)';
            $history->previous = "Null";
            $history->current =  Helpers::getDateFormat($extensionNew->current_due_date);
            $history->comment = "Not Applicable";
            $history->user_id = Auth::user()->id;
            $history->user_name = Auth::user()->name;
            $history->user_role = RoleGroup::where('id', Auth::user()->role)->value('name');
            $history->origin_state = $extensionNew->status;
            $history->change_to =   "Opened";
            $history->change_from = "Initiation";
            $history->action_name = 'Create';
            $history->save();
        }
        if (!empty ($request->proposed_due_date)){
            $history = new ExtensionNewAuditTrail();
            $history->extension_id = $extensionNew->id;
            $history->activity_type = 'Proposed Due Date';
            $history->previous = "Null";
            $history->current =  Helpers::getDateFormat($extensionNew->proposed_due_date);
            $history->comment = "Not Applicable";
            $history->user_id = Auth::user()->id;
            $history->user_name = Auth::user()->name;
            $history->user_role = RoleGroup::where('id', Auth::user()->role)->value('name');
            $history->origin_state = $extensionNew->status;
            $history->change_to =   "Opened";
            $history->change_from = "Initiation";
            $history->action_name = 'Create';
            $history->save();
        }
        if (!empty ($request->description)){
            $history = new ExtensionNewAuditTrail();
            $history->extension_id = $extensionNew->id;
            $history->activity_type = 'Description';
            $history->previous = "Null";
            $history->current = $extensionNew->description;
            $history->comment = "Not Applicable";
            $history->user_id = Auth::user()->id;
            $history->user_name = Auth::user()->name;
            $history->user_role = RoleGroup::where('id', Auth::user()->role)->value('name');
            $history->origin_state = $extensionNew->status;
            $history->change_to =   "Opened";
            $history->change_from = "Initiation";
            $history->action_name = 'Create';
            $history->save();
        }
        if (!empty ($request->justification_reason)){
            $history = new ExtensionNewAuditTrail();
            $history->extension_id = $extensionNew->id;
            $history->activity_type = 'Justification / Reason';
            $history->previous = "Null";
            $history->current = $extensionNew->justification_reason;
            $history->comment = "Not Applicable";
            $history->user_id = Auth::user()->id;
            $history->user_name = Auth::user()->name;
            $history->user_role = RoleGroup::where('id', Auth::user()->role)->value('name');
            $history->origin_state = $extensionNew->status;
            $history->change_to =   "Opened";
            $history->change_from = "Initiation";
            $history->action_name = 'Create';
            $history->save();
        }
        if (!empty ($request->reviewer_remarks)){
            $history = new ExtensionNewAuditTrail();
            $history->extension_id = $extensionNew->id;
            $history->activity_type = 'Reviewer Remarks';
            $history->previous = "Null";
            $history->current = $extensionNew->reviewer_remarks;
            $history->comment = "Not Applicable";
            $history->user_id = Auth::user()->id;
            $history->user_name = Auth::user()->name;
            $history->user_role = RoleGroup::where('id', Auth::user()->role)->value('name');
            $history->origin_state = $extensionNew->status;
            $history->change_to =   "Opened";
            $history->change_from = "Initiation";
            $history->action_name = 'Create';
            $history->save();
        }

        if (!empty ($request->approver_remarks)){
            $history = new ExtensionNewAuditTrail();
            $history->extension_id = $extensionNew->id;
            $history->activity_type = 'Approver Remarks';
            $history->previous = "Null";
            $history->current = $extensionNew->approver_remarks;
            $history->comment = "Not Applicable";
            $history->user_id = Auth::user()->id;
            $history->user_name = Auth::user()->name;
            $history->user_role = RoleGroup::where('id', Auth::user()->role)->value('name');
            $history->origin_state = $extensionNew->status;
            $history->change_to =   "Opened";
            $history->change_from = "Initiation";
            $history->action_name = 'Create';
            $history->save();
        }
        // return redirect()->back()->with('success', 'Induction training data saved successfully!');
        // return redirect()->route('TMS.index')->with('success', 'Induction training data saved successfully!');
        toastr()->success("Record is created Successfully");
        return redirect(url('rcms/qms-dashboard'));

    }

    public function show(Request $request,$id){
        $extensionNew = extension_new::find($id);
        $count = extension_new::where('parent_type' , 'Deviation')->get()->count();
        $capaCount = extension_new::where('parent_type' , 'CAPA')->get()->count();

         
        $reviewers = DB::table('user_roles')
                ->join('users', 'user_roles.user_id', '=', 'users.id')
                ->select('user_roles.q_m_s_processes_id', 'users.id','users.role','users.name') // Include all selected columns in the select statement
                ->where('user_roles.q_m_s_processes_id', 89)
                ->where('user_roles.q_m_s_roles_id', 2)
                ->groupBy('user_roles.q_m_s_processes_id', 'users.id','users.role','users.name') // Include all selected columns in the group by clause
                ->get();
        $approvers = DB::table('user_roles')
                ->join('users', 'user_roles.user_id', '=', 'users.id')
                ->select('user_roles.q_m_s_processes_id', 'users.id','users.role','users.name') // Include all selected columns in the select statement
                ->where('user_roles.q_m_s_processes_id', 89)
                ->where('user_roles.q_m_s_roles_id', 1)
                ->groupBy('user_roles.q_m_s_processes_id', 'users.id','users.role','users.name') // Include all selected columns in the group by clause
                ->get();
        return view('frontend.extension.extension_view', compact('extensionNew','reviewers','capaCount','approvers','count'));

    }

    public function update(Request $request,$id){

        $extensionNew = extension_new::find($id);
        $lastextensionNew = extension_new::find($id);
        $extensionNew->site_location_code = $request->site_location_code;
        $extensionNew->initiator = Auth::user()->id;

        // dd($request->initiator);
        $extensionNew->initiation_date = $request->initiation_date;
        $extensionNew->short_description = $request->short_description;
        $extensionNew->reviewers = $request->reviewers;
        $extensionNew->approvers = $request->approvers;
        $extensionNew->current_due_date = $request->current_due_date;
        $extensionNew->proposed_due_date = $request->proposed_due_date;
        $extensionNew->description = $request->description;
        $extensionNew->justification_reason = $request->justification_reason;
        $extensionNew->file_attachment_extension = $request->file_attachment_extension;
        $extensionNew->reviewer_remarks = $request->reviewer_remarks;
        $extensionNew->file_attachment_reviewer = $request->file_attachment_reviewer;
        $extensionNew->approver_remarks = $request->approver_remarks;
        $extensionNew->file_attachment_approver = $request->file_attachment_approver;
        
        if (!empty ($request->file_attachment_extension)) {
            $files = [];
            if ($request->hasfile('file_attachment_extension')) {
                foreach ($request->file('file_attachment_extension') as $file) {
                    $name = $request->name . 'file_attachment_extension' . rand(1, 100) . '.' . $file->getClientOriginalExtension();
                    $file->move('upload/', $name);
                    $files[] = $name;
                }
            }


            $extensionNew->file_attachment_extension = json_encode($files);
        }

        if (!empty ($request->file_attachment_reviewer)) {
            $files = [];
            if ($request->hasfile('file_attachment_reviewer')) {
                foreach ($request->file('file_attachment_reviewer') as $file) {
                    $name = $request->name . 'file_attachment_reviewer' . rand(1, 100) . '.' . $file->getClientOriginalExtension();
                    $file->move('upload/', $name);
                    $files[] = $name;
                }
            }


            $extensionNew->file_attachment_reviewer = json_encode($files);
        }

        if (!empty ($request->file_attachment_approver)) {
            $files = [];
            if ($request->hasfile('file_attachment_approver')) {
                foreach ($request->file('file_attachment_approver') as $file) {
                    $name = $request->name . 'file_attachment_approver' . rand(1, 100) . '.' . $file->getClientOriginalExtension();
                    $file->move('upload/', $name);
                    $files[] = $name;
                }
            }


            $extensionNew->file_attachment_approver = json_encode($files);
        }
        $extensionNew->save();

        if ($lastextensionNew->short_description != $extensionNew->short_description || !empty ($request->comment)) {
            $lastDocumentAuditTrail = ExtensionNewAuditTrail::where('extension_id', $extensionNew->id)
            ->where('activity_type', 'Short Description')
            ->exists();
            $history = new ExtensionNewAuditTrail;
            $history->extension_id = $id;
            $history->activity_type = 'Short Description';
             $history->previous = $lastextensionNew->short_description;
            $history->current = $extensionNew->short_description;
            $history->comment = $extensionNew->comment;
            $history->user_id = Auth::user()->id;
            $history->user_name = Auth::user()->name;
            $history->user_role = RoleGroup::where('id', Auth::user()->role)->value('name');
            $history->origin_state = $lastextensionNew->status;
            $history->change_to =   "Not Applicable";
            $history->change_from = $lastextensionNew->status;
            $history->action_name = $lastDocumentAuditTrail ? 'Update' : 'New';
            $history->save();
        }

        if ($lastextensionNew->initiator != $extensionNew->initiator || !empty ($request->comment)) {
            // return 'history';
            $history = new ExtensionNewAuditTrail;
            $history->extension_id = $id;
            $history->activity_type = 'Initiator';
             $history->previous = $lastextensionNew->initiator;
            $history->current = $extensionNew->initiator;
            $history->comment = $extensionNew->comment;
            $history->user_id = Auth::user()->id;
            $history->user_name = Auth::user()->name;
            $history->user_role = RoleGroup::where('id', Auth::user()->role)->value('name');
            $history->origin_state = $lastextensionNew->status;
            $history->change_to =   "Not Applicable";
            $history->change_from = $lastextensionNew->status;
             $history->action_name = $lastDocumentAuditTrail ? 'Update' : 'New';
            $history->save();
        }

        if ($lastextensionNew->current_due_date != $extensionNew->current_due_date || !empty ($request->comment)) {
            $lastDocumentAuditTrail = ExtensionNewAuditTrail::where('extension_id', $extensionNew->id)
            ->where('activity_type', 'Current Due Date')
            ->exists();
            $history = new ExtensionNewAuditTrail;
            $history->extension_id = $id;
            $history->activity_type = 'Current Due Date';
             $history->previous = Helpers::getDateFormat($lastextensionNew->current_due_date);
            $history->current =  Helpers::getDateFormat($extensionNew->current_due_date);
            $history->comment = $extensionNew->comment;
            $history->user_id = Auth::user()->id;
            $history->user_name = Auth::user()->name;
            $history->user_role = RoleGroup::where('id', Auth::user()->role)->value('name');
            $history->origin_state = $lastextensionNew->status;
            $history->change_to =   "Not Applicable";
            $history->change_from = $lastextensionNew->status;
            $history->action_name = $lastDocumentAuditTrail ? 'Update' : 'New';;
            $history->save();
        }

        if ($lastextensionNew->proposed_due_date != $extensionNew->proposed_due_date || !empty ($request->comment)) {
            $lastDocumentAuditTrail = ExtensionNewAuditTrail::where('extension_id', $extensionNew->id)
            ->where('activity_type', 'Proposed Due Date')
            ->exists();
            $history = new ExtensionNewAuditTrail;
            $history->extension_id = $id;
            $history->activity_type = 'Proposed Due Date';
             $history->previous =  Helpers::getDateFormat($lastextensionNew->proposed_due_date);
            $history->current =  Helpers::getDateFormat($extensionNew->proposed_due_date);
            $history->comment = $extensionNew->comment;
            $history->user_id = Auth::user()->id;
            $history->user_name = Auth::user()->name;
            $history->user_role = RoleGroup::where('id', Auth::user()->role)->value('name');
            $history->origin_state = $lastextensionNew->status;
            $history->change_to =   "Not Applicable";
            $history->change_from = $lastextensionNew->status;
             $history->action_name = $lastDocumentAuditTrail ? 'Update' : 'New';
            $history->save();
        }

        if ($lastextensionNew->description != $extensionNew->description || !empty ($request->comment)) {
            $lastDocumentAuditTrail = ExtensionNewAuditTrail::where('extension_id', $extensionNew->id)
            ->where('activity_type', 'Description')
            ->exists();
            $history = new ExtensionNewAuditTrail;
            $history->extension_id = $id;
            $history->activity_type = 'Description';
             $history->previous = $lastextensionNew->description;
            $history->current = $extensionNew->description;
            $history->comment = $extensionNew->comment;
            $history->user_id = Auth::user()->id;
            $history->user_name = Auth::user()->name;
            $history->user_role = RoleGroup::where('id', Auth::user()->role)->value('name');
            $history->origin_state = $lastextensionNew->status;
            $history->change_to =   "Not Applicable";
            $history->change_from = $lastextensionNew->status;
             $history->action_name = $lastDocumentAuditTrail ? 'Update' : 'New';
            $history->save();
        }

        if ($lastextensionNew->justification_reason != $extensionNew->justification_reason || !empty ($request->comment)) {
            $lastDocumentAuditTrail = ExtensionNewAuditTrail::where('extension_id', $extensionNew->id)
            ->where('activity_type', 'Justification Reason')
            ->exists();
            $history = new ExtensionNewAuditTrail;
            $history->extension_id = $id;
            $history->activity_type = 'Justification Reason';
             $history->previous = $lastextensionNew->justification_reason;
            $history->current = $extensionNew->justification_reason;
            $history->comment = $extensionNew->comment;
            $history->user_id = Auth::user()->id;
            $history->user_name = Auth::user()->name;
            $history->user_role = RoleGroup::where('id', Auth::user()->role)->value('name');
            $history->origin_state = $lastextensionNew->status;
            $history->change_to =   "Not Applicable";
            $history->change_from = $lastextensionNew->status;
             $history->action_name = $lastDocumentAuditTrail ? 'Update' : 'New';
            $history->save();
        }

        if ($lastextensionNew->reviewer_remarks != $extensionNew->reviewer_remarks || !empty ($request->comment)) {
            $lastDocumentAuditTrail = ExtensionNewAuditTrail::where('extension_id', $extensionNew->id)
            ->where('activity_type', 'Reviewer Remarks')
            ->exists();
            $history = new ExtensionNewAuditTrail;
            $history->extension_id = $id;
            $history->activity_type = 'Reviewer Remarks';
             $history->previous = $lastextensionNew->reviewer_remarks;
            $history->current = $extensionNew->reviewer_remarks;
            $history->comment = $extensionNew->comment;
            $history->user_id = Auth::user()->id;
            $history->user_name = Auth::user()->name;
            $history->user_role = RoleGroup::where('id', Auth::user()->role)->value('name');
            $history->origin_state = $lastextensionNew->status;
            $history->change_to =   "Not Applicable";
            $history->change_from = $lastextensionNew->status;
             $history->action_name = $lastDocumentAuditTrail ? 'Update' : 'New';
            $history->save();
        }

        if ($lastextensionNew->approver_remarks != $extensionNew->approver_remarks || !empty ($request->comment)) {
            $lastDocumentAuditTrail = ExtensionNewAuditTrail::where('extension_id', $extensionNew->id)
            ->where('activity_type', 'Approver Remarks')
            ->exists();
            $history = new ExtensionNewAuditTrail;
            $history->extension_id = $id;
            $history->activity_type = 'Approver Remarks';
             $history->previous = $lastextensionNew->approver_remarks;
            $history->current = $extensionNew->approver_remarks;
            $history->comment = $extensionNew->comment;
            $history->user_id = Auth::user()->id;
            $history->user_name = Auth::user()->name ;
            $history->user_role = RoleGroup::where('id', Auth::user()->role)->value('name');
            $history->origin_state = $lastextensionNew->status;
            $history->change_to =   "Not Applicable";
            $history->change_from = $lastextensionNew->status;
             $history->action_name = $lastDocumentAuditTrail ? 'Update' : 'New';
            $history->save();
        }

        if ($lastextensionNew->site_location_code != $extensionNew->site_location_code || !empty ($request->comment)) {
            $lastDocumentAuditTrail = ExtensionNewAuditTrail::where('extension_id', $extensionNew->id)
            ->where('activity_type', 'Site Location Code')
            ->exists();
            $history = new ExtensionNewAuditTrail;
            $history->extension_id = $id;
            $history->activity_type = 'Site Location Code';
             $history->previous = $lastextensionNew->site_location_code;
            $history->current = Helpers::getDivisionName($extensionNew->site_location_code);
            $history->comment = $extensionNew->comment;
            $history->user_id = Auth::user()->id;
            $history->user_name = Auth::user()->name;
            $history->user_role = RoleGroup::where('id', Auth::user()->role)->value('name');
            $history->origin_state = $lastextensionNew->status;
            $history->change_to =   "Not Applicable";
            $history->change_from = $lastextensionNew->status;
             $history->action_name = $lastDocumentAuditTrail ? 'Update' : 'New';
            $history->save();
        }

        // if ($lastextensionNew->initiation_date != $extensionNew->initiation_date || !empty ($request->comment)) {
        //     $lastDocumentAuditTrail = ExtensionNewAuditTrail::where('extension_id', $extensionNew->id)
        //     ->where('activity_type', 'Initiation Date')
        //     ->exists();
        //     $history = new ExtensionNewAuditTrail;
        //     $history->extension_id = $id;
        //     $history->activity_type = 'Initiation Date';
        //      $history->previous =  Helpers::getDateFormat($lastextensionNew->initiation_date);
        //     $history->current =  Helpers::getDateFormat($extensionNew->initiation_date);
        //     $history->comment = $extensionNew->comment;
        //     $history->user_id = Auth::user()->id;
        //     $history->user_name = Auth::user()->name;
        //     $history->user_role = RoleGroup::where('id', Auth::user()->role)->value('name');
        //     $history->origin_state = $lastextensionNew->status;
        //     $history->change_to =   "Not Applicable";
        //     $history->change_from = $lastextensionNew->status;
        //      $history->action_name = $lastDocumentAuditTrail ? 'Update' : 'New';
        //     $history->save();
        // }

        if ($lastextensionNew->reviewers != $extensionNew->reviewers || !empty ($request->comment)) {
            $lastDocumentAuditTrail = ExtensionNewAuditTrail::where('extension_id', $extensionNew->id)
            ->where('activity_type', 'HOD Reviewer')
            ->exists();
            $history = new ExtensionNewAuditTrail;
            $history->extension_id = $id;
            $history->activity_type = 'HOD Reviewer';
             $history->previous = $lastextensionNew->reviewers;
            $history->current = $extensionNew->reviewers;
            $history->comment = $extensionNew->comment;
            $history->user_id = Auth::user()->id;
            $history->user_name = Auth::user()->name;
            $history->user_role = RoleGroup::where('id', Auth::user()->role)->value('name');
            $history->origin_state = $lastextensionNew->status;
            $history->change_to =   "Not Applicable";
            $history->change_from = $lastextensionNew->status;
             $history->action_name = $lastDocumentAuditTrail ? 'Update' : 'New';
            $history->save();
        }

        if ($lastextensionNew->approvers != $extensionNew->approvers || !empty ($request->comment)) {
            $lastDocumentAuditTrail = ExtensionNewAuditTrail::where('extension_id', $extensionNew->id)
            ->where('activity_type', 'QA Approver')
            ->exists();
            $history = new ExtensionNewAuditTrail;
            $history->extension_id = $id;
            $history->activity_type = 'QA Approver';
             $history->previous = $lastextensionNew->approvers;
            $history->current = $extensionNew->approvers;
            $history->comment = $extensionNew->comment;
            $history->user_id = Auth::user()->id;
            $history->user_name = Auth::user()->name;
            $history->user_role = RoleGroup::where('id', Auth::user()->role)->value('name');
            $history->origin_state = $lastextensionNew->status;
            $history->change_to =   "Not Applicable";
            $history->change_from = $lastextensionNew->status;
             $history->action_name = $lastDocumentAuditTrail ? 'Update' : 'New';
            $history->save();
        }

        if ($lastextensionNew->reviewer_remarks != $extensionNew->reviewer_remarks || !empty ($request->comment)) {
            $lastDocumentAuditTrail = ExtensionNewAuditTrail::where('extension_id', $extensionNew->id)
            ->where('activity_type', 'HOD Remarks')
            ->exists();
            $history = new ExtensionNewAuditTrail;
            $history->extension_id = $id;
            $history->activity_type = 'HOD Remarks';
             $history->previous = $lastextensionNew->reviewer_remarks;
            $history->current = $extensionNew->reviewer_remarks;
            $history->comment = $extensionNew->comment;
            $history->user_id = Auth::user()->id;
            $history->user_name = Auth::user()->name;
            $history->user_role = RoleGroup::where('id', Auth::user()->role)->value('name');
            $history->origin_state = $lastextensionNew->status;
            $history->change_to =   "Not Applicable";
            $history->change_from = $lastextensionNew->status;
             $history->action_name = $lastDocumentAuditTrail ? 'Update' : 'New';
            $history->save();
        }

        if ($lastextensionNew->approver_remarks != $extensionNew->approver_remarks || !empty ($request->comment)) {
            $lastDocumentAuditTrail = ExtensionNewAuditTrail::where('extension_id', $extensionNew->id)
            ->where('activity_type', 'QA Remarks')
            ->exists();
            $history = new ExtensionNewAuditTrail;
            $history->extension_id = $id;
            $history->activity_type = 'QA Remarks';
             $history->previous = $lastextensionNew->approver_remarks;
            $history->current = $extensionNew->approver_remarks;
            $history->comment = $extensionNew->comment;
            $history->user_id = Auth::user()->id;
            $history->user_name = Auth::user()->name;
            $history->user_role = RoleGroup::where('id', Auth::user()->role)->value('name');
            $history->origin_state = $lastextensionNew->status;
            $history->change_to =   "Not Applicable";
            $history->change_from = $lastextensionNew->status;
             $history->action_name = $lastDocumentAuditTrail ? 'Update' : 'New';
            $history->save();
        }

        toastr()->success("Record is created Successfully");
        return redirect()->back();
    }

    public function reject(Request $request,$id){
        try {
            if ($request->username == Auth::user()->email && Hash::check($request->password, Auth::user()->password)) {
                $extensionNew = extension_new::find($id);
                $lastDocument = extension_new::find($id);

                if ($extensionNew->stage == 1) {

                    $extensionNew->stage = "0";
                    $extensionNew->status = "Closed Cancelled";
                    $extensionNew->reject_by = Auth::user()->name;
                    $extensionNew->reject_on = Carbon::now()->format('d-M-Y');
                    $extensionNew->reject_comment = $request->comment;

                    
                    $extensionNew->update();
                    return back();
                }
             
               
            } else {
                toastr()->error('E-signature Not match');
                return back();
            }
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
    public function moreinfoStateChange(Request $request,$id){
        try {
            if ($request->username == Auth::user()->email && Hash::check($request->password, Auth::user()->password)) {
                $extensionNew = extension_new::find($id);
                $lastDocument = extension_new::find($id);

                if ($extensionNew->stage == 2) {

                    $extensionNew->stage = "1";
                    $extensionNew->status = "Opened";
                    $extensionNew->more_info_review_by = Auth::user()->name;
                    $extensionNew->more_info_review_on = Carbon::now()->format('d-M-Y');
                    $extensionNew->more_info_review_comment = $request->comment;

                    
                    $extensionNew->update();
                    toastr()->success('Document Sent');
                    return back();
                }
                if ($extensionNew->stage == 3) {


                    $extensionNew->stage = "2";
                    $extensionNew->status = "In Review";
                    $extensionNew->more_info_inapproved_by = Auth::user()->name;
                    $extensionNew->more_info_inapproved_on = Carbon::now()->format('d-M-Y');
                    $extensionNew->more_info_inapproved_comment = $request->comment;

                   

                    $extensionNew->update();
                    toastr()->success('Document Sent');
                    return back();
                }
               
            } else {
                toastr()->error('E-signature Not match');
                return back();
            }
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
    public function sendstage(Request $request,$id){
        try {
            if ($request->username == Auth::user()->email && Hash::check($request->password, Auth::user()->password)) {
                $extensionNew = extension_new::find($id);
                $lastDocument = extension_new::find($id);

                if ($extensionNew->stage == 1) {

                    $extensionNew->stage = "2";
                    $extensionNew->status = "In Review";
                    $extensionNew->submit_by = Auth::user()->name;
                    $extensionNew->submit_on = Carbon::now()->format('d-M-Y');
                    $extensionNew->submit_comment = $request->comment;

                    $history = new ExtensionNewAuditTrail();
                    $history->extension_id = $id;
                    $history->activity_type = 'Activity Log';
                    $history->previous = "";
                    $history->action='Submit';
                    $history->current = $extensionNew->submit_by;
                    $history->comment = $request->comment;
                    $history->user_id = Auth::user()->id;
                    $history->user_name = Auth::user()->name;
                    $history->user_role = RoleGroup::where('id', Auth::user()->role)->value('name');
                    $history->origin_state = $lastDocument->status;
                    $history->change_to =   "In Review";
                    $history->change_from = $lastDocument->status;
                    $history->stage = 'Plan Proposed';
                    $history->save();


                    // $list = Helpers::getHodUserList();
                    // foreach ($list as $u) {
                    //     if ($u->q_m_s_divisions_id == $extensionNew->division_id) {
                    //         $email = Helpers::getInitiatorEmail($u->user_id);
                    //         if ($email !== null) {

                    //             try {
                    //                 Mail::send(
                    //                     'mail.view-mail',
                    //                     ['data' => $extensionNew],
                    //                     function ($message) use ($email) {
                    //                         $message->to($email)
                    //                             ->subject("Activity Performed By " . Auth::user()->name);
                    //                     }
                    //                 );
                    //             } catch (\Exception $e) {
                    //                 //log error
                    //             }
                    //         }
                    //     }
                    // }

                    // $list = Helpers::getHeadoperationsUserList();
                    // foreach ($list as $u) {
                    //     if ($u->q_m_s_divisions_id == $extensionNew->division_id) {
                    //         $email = Helpers::getInitiatorEmail($u->user_id);
                    //         if ($email !== null) {

                    //             Mail::send(
                    //                 'mail.Categorymail',
                    //                 ['data' => $extensionNew],
                    //                 function ($message) use ($email) {
                    //                     $message->to($email)
                    //                         ->subject("Activity Performed By " . Auth::user()->name);
                    //                 }
                    //             );
                    //         }
                    //     }
                    // }
                    // dd($extensionNew);
                    $extensionNew->update();
                    return back();
                }
                if ($extensionNew->stage == 2) {
                    $extensionNew->stage = "3";
                    $extensionNew->status = "In Approved";
                    $extensionNew->submit_by_review = Auth::user()->name;
                    $extensionNew->submit_on_review = Carbon::now()->format('d-M-Y');
                    $extensionNew->submit_comment_review = $request->comment;

                    $history = new ExtensionNewAuditTrail();
                    $history->extension_id = $id;
                    $history->activity_type = 'Activity Log';
                    $history->previous = "";
                    $history->current = $extensionNew->HOD_Review_Complete_By;
                    $history->comment = $request->comment;
                    $history->action= 'Review';
                    $history->user_id = Auth::user()->id;
                    $history->user_name = Auth::user()->name;
                    $history->user_role = RoleGroup::where('id', Auth::user()->role)->value('name');
                    $history->origin_state = $lastDocument->status;
                    $history->change_to =   "In Approved";
                    $history->change_from = $lastDocument->status;
                    $history->stage = 'Plan Approved';
                    $history->save();

                    // dd($history->action);
                    // $list = Helpers::getQAUserList();
                    // foreach ($list as $u) {
                    //     if ($u->q_m_s_divisions_id == $extensionNew->division_id) {
                    //         $email = Helpers::getInitiatorEmail($u->user_id);
                    //         if ($email !== null) {
                    //             try {
                    //                 Mail::send(
                    //                     'mail.view-mail',
                    //                     ['data' => $extensionNew],
                    //                     function ($message) use ($email) {
                    //                         $message->to($email)
                    //                             ->subject("Activity Performed By " . Auth::user()->name);
                    //                     }
                    //                 );
                    //             } catch (\Exception $e) {
                    //                 //log error
                    //             }
                    //         }
                    //     }
                    // }
                    $extensionNew->update();
                    toastr()->success('Document Sent');
                    return back();
                }      
             
                if ($extensionNew->stage == 3) {

                    $extensionNew->stage = "4";
                    $extensionNew->status = "Closed - Reject";


                    $extensionNew->submit_by_inapproved = Auth::user()->name;
                    $extensionNew->submit_on_inapproved = Carbon::now()->format('d-M-Y');
                    $extensionNew->submit_commen_inapproved = $request->comment;

                    $history = new ExtensionNewAuditTrail();
                    $history->extension_id = $id;
                    $history->activity_type = 'Activity Log';
                    $history->previous = "";
                    $history->action= 'Approved';
                    $history->current = $extensionNew->QA_Initial_Review_Complete_By;
                    $history->comment = $request->comment;
                    $history->user_id = Auth::user()->id;
                    $history->user_name = Auth::user()->name;
                    $history->change_to =   "Closed - Reject";
                    $history->change_from = $lastDocument->status;
                    $history->user_role = RoleGroup::where('id', Auth::user()->role)->value('name');
                    $history->origin_state = $lastDocument->status;
                    $history->stage = 'Completed';
                    $history->save();
                    // $list = Helpers::getQAUserList();
                    // foreach ($list as $u) {
                    //     if ($u->q_m_s_divisions_id == $extensionNew->division_id) {
                    //         $email = Helpers::getInitiatorEmail($u->user_id);
                    //         if ($email !== null) {
                    //             try {
                    //                 Mail::send(
                    //                     'mail.view-mail',
                    //                     ['data' => $extensionNew],
                    //                     function ($message) use ($email) {
                    //                         $message->to($email)
                    //                             ->subject("Activity Performed By " . Auth::user()->name);
                    //                     }
                    //                 );
                    //             } catch (\Exception $e) {
                    //                 //log error
                    //             }
                    //         }
                    //     }
                    // }
                    $extensionNew->update();
                    toastr()->success('Document Sent');
                    return back();
                }
                
            } else {
                toastr()->error('E-signature Not match');
                return back();
            }
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
public function sendCQA(Request $request,$id)
{
    try {
        if ($request->username == Auth::user()->email && Hash::check($request->password, Auth::user()->password)) {
            $extensionNew = extension_new::find($id);
            $lastDocument = extension_new::find($id);

            if ($extensionNew->stage == 3) {

                $extensionNew->stage = "5";
                $extensionNew->status = "In CQA Approval";


                $extensionNew->send_cqa_by = Auth::user()->name;
                $extensionNew->send_cqa_on = Carbon::now()->format('d-M-Y');
                $extensionNew->send_cqa_comment = $request->comment;

                // $history = new DeviationAuditTrail();
                // $history->deviation_id = $id;
                // $history->activity_type = 'Activity Log';
                // $history->previous = "";
                // $history->action= 'QA Initial Review Complete';
                // $history->current = $extensionNew->QA_Initial_Review_Complete_By;
                // $history->comment = $request->comment;
                // $history->user_id = Auth::user()->id;
                // $history->user_name = Auth::user()->name;
                // $history->change_to =   "CFT Review";
                // $history->change_from = $lastDocument->status;
                // $history->user_role = RoleGroup::where('id', Auth::user()->role)->value('name');
                // $history->origin_state = $lastDocument->status;
                // $history->stage = 'Completed';
                // $history->save();
                // $list = Helpers::getQAUserList();
                // foreach ($list as $u) {
                //     if ($u->q_m_s_divisions_id == $extensionNew->division_id) {
                //         $email = Helpers::getInitiatorEmail($u->user_id);
                //         if ($email !== null) {
                //             try {
                //                 Mail::send(
                //                     'mail.view-mail',
                //                     ['data' => $extensionNew],
                //                     function ($message) use ($email) {
                //                         $message->to($email)
                //                             ->subject("Activity Performed By " . Auth::user()->name);
                //                     }
                //                 );
                //             } catch (\Exception $e) {
                //                 //log error
                //             }
                //         }
                //     }
                // }
                $extensionNew->update();
                toastr()->success('Document Sent');
                return back();
            }
           
            if ($extensionNew->stage == 5) {

                $extensionNew->stage = "6";
                $extensionNew->status = "Closed - Done";


                $extensionNew->cqa_approval_by = Auth::user()->name;
                $extensionNew->cqa_approval_on = Carbon::now()->format('d-M-Y');
                $extensionNew->cqa_approval_comment = $request->comment;

                // $history = new DeviationAuditTrail();
                // $history->deviation_id = $id;
                // $history->activity_type = 'Activity Log';
                // $history->previous = "";
                // $history->action= 'QA Initial Review Complete';
                // $history->current = $extensionNew->QA_Initial_Review_Complete_By;
                // $history->comment = $request->comment;
                // $history->user_id = Auth::user()->id;
                // $history->user_name = Auth::user()->name;
                // $history->change_to =   "CFT Review";
                // $history->change_from = $lastDocument->status;
                // $history->user_role = RoleGroup::where('id', Auth::user()->role)->value('name');
                // $history->origin_state = $lastDocument->status;
                // $history->stage = 'Completed';
                // $history->save();
                // $list = Helpers::getQAUserList();
                // foreach ($list as $u) {
                //     if ($u->q_m_s_divisions_id == $extensionNew->division_id) {
                //         $email = Helpers::getInitiatorEmail($u->user_id);
                //         if ($email !== null) {
                //             try {
                //                 Mail::send(
                //                     'mail.view-mail',
                //                     ['data' => $extensionNew],
                //                     function ($message) use ($email) {
                //                         $message->to($email)
                //                             ->subject("Activity Performed By " . Auth::user()->name);
                //                     }
                //                 );
                //             } catch (\Exception $e) {
                //                 //log error
                //             }
                //         }
                //     }
                // }
                $extensionNew->update();
                toastr()->success('Document Sent');
                return back();
            }
        } else {
            toastr()->error('E-signature Not match');
            return back();
        }
    } catch (\Throwable $th) {
        return response()->json([
            'success' => false,
            'message' => $th->getMessage()
        ], 500);
    }
}
public static function sendApproved(Request $request,$id)
{
    try {
        if ($request->username == Auth::user()->email && Hash::check($request->password, Auth::user()->password)) {
            $extensionNew = extension_new::find($id);
            $lastDocument = extension_new::find($id);

            if ($extensionNew->stage == 3) {

                    $extensionNew->stage = "6";
                    $extensionNew->status = "Closed - Done";


                    $extensionNew->submit_by_approved = Auth::user()->name;
                    $extensionNew->submit_on_approved = Carbon::now()->format('d-M-Y');
                    $extensionNew->submit_comment_approved = $request->comment;

                    
                    $extensionNew->update();
                    toastr()->success('Document Sent');
                    return back();
                }
        } else {
            toastr()->error('E-signature Not match');
            return back();
        }
    } catch (\Throwable $th) {
        return response()->json([
            'success' => false,
            'message' => $th->getMessage()
        ], 500);
    }
}
    
    public static function singleReport($id)
    {
        $data = extension_new::find($id);
        if (!empty($data)) {
            $data->originator = User::where('id', $data->initiator)->value('name');
            $pdf = App::make('dompdf.wrapper');
            $time = Carbon::now();
            $pdf = PDF::loadview('frontend.extension.singleReportNew', compact('data'))
                ->setOptions([
                    'defaultFont' => 'sans-serif',
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => true,
                    'isPhpEnabled' => true,
                ]);
            $pdf->setPaper('A4');
            $pdf->render();
            $canvas = $pdf->getDomPDF()->getCanvas();
            $height = $canvas->get_height();
            $width = $canvas->get_width();
            $canvas->page_script('$pdf->set_opacity(0.1,"Multiply");');
            $canvas->page_text($width / 4, $height / 2, $data->status, null, 25, [0, 0, 0], 2, 6, -20);
            return $pdf->stream('Extension' . $id . '.pdf');
        }
    }
    public function extensionNewAuditTrail($id)
    {
        $audit = ExtensionNewAuditTrail::where('extension_id', $id)->orderByDesc('id')->paginate(5);
        // dd($audit);
        $today = Carbon::now()->format('d-m-y');
        $document = extension_new::where('id', $id)->first();
        $document->initiator = User::where('id', $document->initiator)->value('name');
        return view('frontend.extension.audit_trailNew', compact('audit', 'document', 'today'));
    }

    public function auditReportext($id)
    {
        $doc = extension_new::find($id);
        if (!empty($doc)) {
            $doc->originator = User::where('id', $doc->initiator_id)->value('name');
            $data = ExtensionNewAuditTrail::where('extension_id', $doc->id)->orderByDesc('id')->get();
    
            $pdf = App::make('dompdf.wrapper');
            $time = Carbon::now();
            $pdf = PDF::loadview('frontend.extension.auditReport', compact('data', 'doc'))
                ->setOptions([
                    'defaultFont' => 'sans-serif',
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => true,
                    'isPhpEnabled' => true,
                ]);
            $pdf->setPaper('A4');
            $pdf->render();
            $canvas = $pdf->getDomPDF()->getCanvas();
            $height = $canvas->get_height();
            $width = $canvas->get_width();
    
            $canvas->page_script('$pdf->set_opacity(0.1,"Multiply");');
    
            $canvas->page_text(
                $width / 3,
                $height / 2,
                $doc->status,
                null,
                60,
                [0, 0, 0],
                2,
                6,
                -20
            );
            return $pdf->stream('SOP' . $id . '.pdf');
        }
    }
}
