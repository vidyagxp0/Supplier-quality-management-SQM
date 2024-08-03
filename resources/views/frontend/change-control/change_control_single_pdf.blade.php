<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Connexo - Software</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
</head>

<style>
    body {
        font-family: 'Roboto', sans-serif;
        margin: 0;
        padding: 0;
        min-height: 100vh;
    }

    .w-10 {
        width: 10%;
    }

    .w-20 {
        width: 20%;
    }

    .w-25 {
        width: 25%;
    }

    .w-30 {
        width: 30%;
    }

    .w-40 {
        width: 40%;
    }

    .w-50 {
        width: 50%;
    }

    .w-60 {
        width: 60%;
    }

    .w-70 {
        width: 70%;
    }

    .w-80 {
        width: 80%;
    }

    .w-90 {
        width: 90%;
    }

    .w-100 {
        width: 100%;
    }

    .h-100 {
        height: 100%;
    }

    header table,
    header th,
    header td,
    footer table,
    footer th,
    footer td,
    .border-table table,
    .border-table th,
    .border-table td {
        border: 1px solid black;
        border-collapse: collapse;
        font-size: 0.9rem;
        vertical-align: middle;
    }

    table {
        width: 100%;
    }

    th,
    td {
        padding: 10px;
        text-align: left;
    }

    footer .head,
    header .head {
        text-align: center;
        font-weight: bold;
        font-size: 1.2rem;
    }

    @page {
        size: A4;
        margin-top: 160px;
        margin-bottom: 60px;
    }

    header {
        position: fixed;
        top: -140px;
        left: 0;
        width: 100%;
        display: block;
    }

    footer {
        width: 100%;
        position: fixed;
        display: block;
        bottom: -40px;
        left: 0;
        font-size: 0.9rem;
    }

    footer td {
        text-align: center;
    }

    .inner-block {
        padding: 10px;
    }

    .inner-block tr {
        font-size: 0.8rem;
    }

    .inner-block .block {
        margin-bottom: 30px;
    }

    .inner-block .block-head {
        font-weight: bold;
        font-size: 1.1rem;
        padding-bottom: 5px;
        border-bottom: 2px solid #4274da;
        margin-bottom: 10px;
        color: #4274da;
    }

    .inner-block th,
    .inner-block td {
        vertical-align: baseline;
    }

    .table_bg {
        background: #4274da57;
    }
</style>

<body>

    <header>
        <table>
            <tr>
                <td class="w-70 head">
                    Change Control Single Report
                </td>
                <td class="w-30">
                    <div class="logo">
                        <img src="https://www.connexo.io/assets/img/logo/logo.png" alt="" class="w-100">
                    </div>
                </td>
            </tr>
        </table>
        <table>
            <tr>
                <td class="w-30">
                    <strong>Change Control No.</strong>
                </td>
                <td class="w-40">
                    {{ Helpers::getDivisionName($data->division_id) }}/CC/{{ date('Y') }}/{{ str_pad($data->record, 4, '0', STR_PAD_LEFT) }}
                </td>
                <td class="w-30">
                    <strong>Record No.</strong> {{ str_pad($data->record, 4, '0', STR_PAD_LEFT) }}
                </td>
            </tr>
        </table>
    </header>

    <div class="inner-block">
        <div class="content-table">
            <div class="block">
                <div class="block-head">
                    General Information
                </div>
                <table>
                    <tr> On {{ Helpers::getDateFormat($data->created_at) }} added by {{ $data->originator }}
                        <th class="w-20">Initiator</th>
                        <td class="w-30">{{ $data->originator }}</td>

                        <th class="w-20">Date Initiation</th>
                        <td class="w-30">{{ $data->intiation_date }}</td>
                    </tr>

                    <tr>
                        <th class="w-20">Assigned To</th>
                        <td class="w-30">
                            @if ($data->assign_to)
                                {{ Helpers::getInitiatorName($data->assign_to) }}
                            @else
                                Not Applicable
                            @endif
                        </td>

                        <th class="w-20">CFT Reviewer</th>
                        <td class="w-30">{{ $data->cft_reviewer }}</td>
                    <tr>

                        <th class="w-20">CFT Reviewer Person </th>
                        <td class="w-30">{{ $users }}</td>

                        <th class="w-20">Due Date</th>
                        <td class="w-30" colspan="3">
                            @if ($data->due_date)
                                {{ $data->due_date }}
                            @else
                                Not Applicable
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th class="w-20">Initiator Group</th>
                        <td class="w-30">
                            @if ($data->initiator_group_code)
                                {{ Helpers::getFullDepartmentName($data->initiator_group_code) }}
                            @else
                                Not Applicable
                            @endif
                        </td>
                        <th class="w-20">Initiator Group Code</th>
                        <td class="w-30" colspan="3">
                            @if ($data->initiator_group_code)
                                {{ $data->initiator_group_code }}
                            @else
                                Not Applicable
                            @endif
                        </td>
                    </tr>

                    <tr>
                        <th class="w-20">Short Description</th>
                        <td class="w-80" colspan="3">
                            @if ($data->short_description)
                                {{ $data->short_description }}
                            @else
                                Not Applicable
                            @endif
                        </td>
                    </tr>

                    <tr>
                        <th class="w-20">Severity Level</th>
                        <td class="w-30">
                            @if ($data->severity_level1)
                                {{ ucfirst($data->severity_level1) }}
                            @else
                                Not Applicable
                            @endif
                        </td>

                        <th class="w-20">Initiated Through</th>
                        <td class="w-30">
                            @if ($data->initiated_through)
                                {{ ucfirst($data->initiated_through) }}
                            @else
                                Not Applicable
                            @endif
                        </td>
                    </tr>

                    <tr>                        
                        <th class="w-20">Others</th>
                        <td class="w-80" colspan="3">
                            @if ($data->initiated_through_req)
                                {{ $data->initiated_through_req }}
                            @else
                                Not Applicable
                            @endif
                        </td>
                    </tr>

                    <tr>
                        <th class="w-20">Repeat</th>
                        <td class="w-30">
                            @if ($data->repeat)
                                {{ ucfirst($data->repeat) }}
                            @else
                                Not Applicable
                            @endif
                        </td> 
                    </tr>

                    <tr>
                        <th class="w-20">Repeat Nature</th>
                        <td class="w-80" colspan="3">
                            @if ($data->repeat_nature)
                                {{ $data->repeat_nature }}
                            @else
                                Not Applicable
                            @endif
                        </td>
                    </tr>

                    <tr>
                        <th class="w-20">Nature of Change</th>
                        <td class="w-30">
                            @if ($data->nature_Change)
                                {{ $data->nature_Change }}
                            @else
                                Not Applicable
                            @endif
                        </td>
                    </tr>

                    <tr> 
                        <th class="w-20">If Others</th>
                        <td class="w-80" colspan="3">
                            @if ($data->If_Others)
                                {{ $data->If_Others }}
                            @else
                                Not Applicable
                            @endif
                        </td>
                    </tr>

                    <tr>
                        <th class="w-20">Division Code</th>
                        <td class="w-30">
                            @if ($data->Division_Code)
                                {{ $data->Division_Code }}
                            @else
                                Not Applicable
                            @endif
                        </td>
                    </tr>


                </table>
                <div class="border-table">
                    <div class="block-head">
                        Initial Attachment
                    </div>
                    <table>
                        <tr class="table_bg">
                            <th class="w-20">S.N.</th>
                            <th class="w-60">Attachment</th>
                        </tr>
                        @if ($data->in_attachment)
                            @foreach (json_decode($data->in_attachment) as $key => $file)
                                <tr>
                                    <td class="w-20">{{ $key + 1 }}</td>
                                    <td class="w-20"><a href="{{ asset('upload/' . $file) }}"
                                            target="_blank"><b>{{ $file }}</b></a> </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td class="w-20">1</td>
                                <td class="w-20">Not Applicable</td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>

            <!-- Change Details Tab -->
            <div class="block">
                <div class="block-head">
                    Change Details
                </div>
                <table>
                    <tr>
                        <th class="w-20">Current Practice</th>
                        <td>

                            <div>
                                @if ($data->current_practice)
                                    {{ $data->current_practice }}
                                @else
                                    Not Applicable
                                @endif
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th class="w-20">Proposed Change</th>
                        <td>

                            <div>
                                @if ($data->proposed_change)
                                    {{ $data->proposed_change }}
                                @else
                                    Not Applicable
                                @endif
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th class="w-20">Reason For Change</th>
                        <td>

                            <div>
                                @if ($data->reason_change)
                                    {{ $data->reason_change }}
                                @else
                                    Not Applicable
                                @endif
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th class="w-20">Any Other Comments</th>
                        <td>

                            <div>
                                @if ($data->other_comment)
                                    {{ $data->other_comment }}
                                @else
                                    Not Applicable
                                @endif
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th class="w-20">Supervisor Comments</th>
                        <td>

                            <div>
                                @if ($data->supervisor_comment)
                                    {{ $data->supervisor_comment }}
                                @else
                                    Not Applicable
                                @endif
                            </div>
                        </td>
                    </tr>

                </table>
            </div>
            
            <div class="block">
                <div class="head">
                    <div class="block-head">
                        QA Review
                    </div>
                    <table>
                        <tr>
                            <th class="w-20">Type of Change</th>
                            <td class="w-80">{{ ucfirst($data->type_chnage) }}</td>
                        </tr>

                        <tr>
                            <th class="w-20">QA Review Comments</th>
                            <td>
                                <div>
                                    {{ $data->qa_comments }}
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th class="w-20">Related Records</th>
                            <td class="w-80"> {{ $data->related_records }}</td>
                        </tr>

                    </table>
                    <div class="border-table">
                        <div class="block-head">
                            QA Attachments
                        </div>
                        <table>

                            <tr class="table_bg">
                                <th class="w-20">S.N.</th>
                                <th class="w-80">Attachment</th>
                            </tr>
                            @if ($data->qa_head)
                                @foreach (json_decode($data->qa_head) as $key => $file)
                                    <tr>
                                        <td class="w-20">{{ $key + 1 }}</td>
                                        <td class="w-20"><a href="{{ asset('upload/' . $file) }}"
                                                target="_blank"><b>{{ $file }}</b></a> </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td class="w-20">1</td>
                                    <td class="w-20">Not Applicable</td>
                                </tr>
                            @endif

                        </table>
                    </div>
                </div>
            </div>
            <div class="block">
                <div class="head">
                    <div class="block-head">
                        Evaluation Details
                    </div>
                    <table>
                        <tr>
                            <th class="w-20">QA Evaluation Comments</th>
                            <td>
                </div>
                <div>
                    {{ $data->qa_eval_comments }}
                </div>
                </td>
                </tr>



                <tr>
                    <th class="w-20">Training Required</th>
                    <td class="w-80"> {{ ucfirst($data->training_required) }}</td>
                </tr>
                <tr>
                    <th class="w-20">Training Comments</th>
                    <td>
            </div>
            <div>
                {{ $data->train_comments }}
            </div>
            </td>
            </tr>
            </table>
            <table>
                <div class="border-table">
                    <div class="block-head">
                        QA Evaluation Attachments
                    </div>
                    <table>

                        <tr class="table_bg">
                            <th class="w-20">S.N.</th>
                            <th class="w-80">Attachment</th>
                        </tr>
                        @if ($data->qa_eval_attach)
                            @foreach (json_decode($data->qa_eval_attach) as $key => $file)
                                <tr>
                                    <td class="w-20">{{ $key + 1 }}</td>
                                    <td class="w-20"><a href="{{ asset('upload/' . $file) }}"
                                            target="_blank"><b>{{ $file }}</b></a> </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td class="w-20">1</td>
                                <td class="w-20">Not Applicable</td>
                            </tr>
                        @endif

                    </table>
                </div>
            </table>

            <div class="block">
                <div class="head">
                    <div class="block-head">
                        Feedback
                    </div>
                    <table>
                        <tr>
                            <th class="w-20">Comments</th>
                            <td class="w-80" colspan="3">{{ $data->cft_comments }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="border-table">
                <div class="block-head">
                    Attachments
                </div>
                <table>

                    <tr class="table_bg">
                        <th class="w-20">S.N.</th>
                        <th class="w-60">Attachment</th>
                    </tr>
                    @if ($data->group_attachments)
                        @foreach (json_decode($data->group_attachments) as $key => $file)
                            <tr>
                                <td class="w-60">{{ $key + 1 }}</td>
                                <td class="w-60"><a href="{{ asset('upload/' . $file) }}"
                                        target="_blank"><b>{{ $file }}</b></a> </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td class="w-20">1</td>
                            <td class="w-20">Not Applicable</td>
                        </tr>
                    @endif

                </table>

            </div>
            <div>
                <table>
                    <tr>
                        <th class="w-20">QA Comments</th>
                        <td class="w-80">
                            <div>
                                {{ $data->qa_comments }}
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th class="w-20">QA Head Designee Comments</th>
                        <td class="w-80">

            </div>
            <div>
                {{ $data->designee_comments }}
            </div>
            </td>
            </tr>
            <tr>
                <th class="w-20">Warehouse Comments</th>
                <td class="w-80">

        </div>
        <div>
            {{ $data->Warehouse_comments }}
        </div>
        </td>
        </tr>
        <tr>
            <th class="w-20">Engineering Comments</th>
            <td class="w-80">

    </div>
    <div>
        {{ $data->Engineering_comments }}
    </div>
    </td>
    </tr>
    <tr>
        <th class="w-20">Instrumentation Comments</th>
        <td class="w-80">

            </div>
            <div>
                {{ $data->Instrumentation_comments }}
            </div>
        </td>
    </tr>
    <tr>
        <th class="w-20">Validation Comments</th>
        <td class="w-80">

            </div>
            <div>
                {{ $data->Validation_comments }}
            </div>
        </td>
    </tr>
    <tr>
        <th class="w-20">Others Comments</th>
        <td class="w-80">

            </div>
            <div>
                {{ $data->Others_comments }}
            </div>
        </td>
    </tr>
    <tr>
        <th class="w-20">Comments</th>
        <td class="w-80">

            </div>
            <div>
                {{ $data->Group_comments }}
            </div>
        </td>
    </tr>
    </table>
    <div class="border-table">
        <div class="block-head">
            Attachments
        </div>

        <table>

            <tr class="table_bg">
                <th class="w-20">S.N.</th>
                <th class="w-80">Attachment</th>
            </tr>
            @if ($data->qa_head)
                @foreach (json_decode($data->qa_head) as $key => $file)
                    <tr>
                        <td class="w-20">{{ $key + 1 }}</td>
                        <td class="w-20"><a href="{{ asset('upload/' . $file) }}"
                                target="_blank"><b>{{ $file }}</b></a> </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td class="w-20">1</td>
                    <td class="w-20">Not Applicable</td>
                </tr>
            @endif

        </table>
    </div>

    </table>
    <div class="block">
        <div class="block-head">
            Risk Assessment
        </div>
        <table>
            <tr>
                <th class="w-20">Risk Identification</th>
                <td class="w-80" colspan="3">
                    <!-- <div><strong>On {{ Helpers::getDateFormat($data->created_at) }} added by
                            {{ $data->originator }}</strong></div> -->
                    <div>
                        {{ $data->risk_identification }}
                    </div>
                </td>
            </tr>
            <tr>
                <th class="w-20">Severity</th>
                <td class="w-30">
                    @if($data->severity == 1)
                        Negligible
                    @elseif($data->severity == 2)
                        Minor
                    @elseif($data->severity == 3)
                        Moderate
                    @elseif($data->severity == 4)
                        Major
                    @else
                        Fatel
                    @endif
                </td>

                <th class="w-20">Occurance</th>
                <td class="w-30">
                    @if($data->Occurance == 1)
                        Extremely Unlikely
                    @elseif($data->Occurance == 2)
                        Rare
                    @elseif($data->Occurance == 3)
                        Unlikely
                    @elseif($data->Occurance == 4)
                        Likely
                    @else
                        Very Likely
                    @endif
                </td>
            </tr>
            <tr>
                <th class="w-20">Detection</th>
                <td class="w-30">
                    @if($data->Detection == 1)
                        Impossible
                    @elseif($data->Detection == 2)
                        Rare
                    @elseif($data->Detection == 3)
                        Unlikely
                    @else
                        Very Likely
                    @endif
                </td>

                <th class="w-20">RPN</th>
                <td class="w-30"> {{ $data->RPN }}</td>
            </tr>

            <tr>
                <th class="w-20">Risk Evaluation</th>
                <td class="w-80" colspan="3">
                    <div>
                        {{ $data->risk_evaluation }}
                    </div>
                </td>
            </tr>

            <tr>
                <th class="w-20">Mitigation Action</th>
                <td class="w-80" colspan="3">
                    <div>
                        {{ $data->migration_action }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="block">
        <div class="block-head">
            QA Approval Comments
        </div>
        <table>
            <tr>
                <th class="w-20">QA Approval Comments</th>
                <td class="w-80">
                    <div>
                        {{ $data->risk_identification }}
                    </div>
                </td>
            </tr>
            <tr>
                <th class="w-20">Training Feedback</th>
                <td class="w-80">
    </div>
    <div>
        {{ $data->feedback }}
    </div>
    </td>
    </tr>

    </table>
    <div class="border-table">
        <div class="block-head">
            Training Attachments
        </div>
        <table>

            <tr class="table_bg">
                <th class="w-20">S.N.</th>
                <th class="w-60">Attachment</th>
            </tr>
            @if ($data->tran_attach)
                @foreach (json_decode($data->tran_attach) as $key => $file)
                    <tr>
                        <td class="w-20">{{ $key + 1 }}</td>
                        <td class="w-20"><a href="{{ asset('upload/' . $file) }}"
                                target="_blank"><b>{{ $file }}</b></a> </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td class="w-20">1</td>
                    <td class="w-20">Not Applicable</td>
                </tr>
            @endif

        </table>
    </div>
    </div>
    <div class="block">
        <div class="block-head">
            Change Closure
        </div>
        <table>
            <!-- <tr>
                <th class="w-20">Affected Documents+</th>
                <td class="w-80">
                    <div><strong>On {{ Helpers::getDateFormat($data->created_at) }} added by
                        {{ $data->originator }}</strong>
                </div>
                    <div>
                        {{ $data->ann }}
                    </div>
                </td>
            </tr> -->
            <tr>
                <th class="w-20">QA Closure Comments</th>
                <td class="w-30"> {{ $data->qa_closure_comments }}</td>
            </tr>
        </table>
        <div class="border-table">
            <div class="block-head">
                Initial Attachment
            </div>
            <table>

                <tr class="table_bg">
                    <th class="w-20">S.N.</th>
                    <th class="w-60">Attachment</th>
                </tr>
                @if ($data->in_attachment)
                    @foreach (json_decode($data->in_attachment) as $key => $file)
                        <tr>
                            <td class="w-20">{{ $key + 1 }}</td>
                            <td class="w-20"><a href="{{ asset('upload/' . $file) }}"
                                    target="_blank"><b>{{ $file }}</b></a> </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td class="w-20">1</td>
                        <td class="w-20">Not Applicable</td>
                    </tr>
                @endif

            </table>
        </div>
        </table>


    </div>
    </div>
    <div class="block">
        <div class="block-head">
            Extension Justification
        </div>
        <table>
            <tr>
                <th class="w-20">Due Date Extension Justification</th>
                <td class="w-30"> {{ $data->qa_closure_comments }}</td>
            </tr>
        </table>
    </div>


    <div class="block">
        <div class="block-head">
            Activity Log
        </div>
        <table>
            <tr>
                <th class="w-20">Submitted By</th>
                <td class="w-30">
                    @if ($data->submitted_by)
                        {{ $data->submitted_by }}
                    @else
                        Not Applicable
                    @endif
                </td>
                <th class="w-20">Submitted On</th>
                <td class="w-30">
                    @if ($data->submitted_on)
                        {{ $data->submitted_on }}
                    @else
                        Not Applicable
                    @endif
                </td>
                <th class="w-20">Submitted Comment</th>
                <td class="w-30">
                    @if ($data->submitted_comment)
                        {{ $data->submitted_comment }}
                    @else
                        Not Applicable
                    @endif
                </td>
            </tr>

            <tr>
                <th class="w-20">HOD Review Completed By</th>
                <td class="w-30">
                    @if ($data->hod_review_completed_by)
                        {{ $data->hod_review_completed_by }}
                    @else
                        Not Applicable
                    @endif
                </td>
                <th class="w-20">HOD Review Completed On</th>
                <td class="w-30">
                    @if ($data->hod_review_completed_on)
                        {{ $data->hod_review_completed_on }}
                    @else
                        Not Applicable
                    @endif
                </td>
                <th class="w-20">HOD Review Completed Comment</th>
                <td class="w-30">
                    @if ($data->hod_review_completed_comment)
                        {{ $data->hod_review_completed_comment }}
                    @else
                        Not Applicable
                    @endif
                </td>
            </tr>

            <tr>
                <th class="w-20">Pending CFT Review Completed By</th>
                <td class="w-30">
                    @if ($data->cft_review_by)
                        {{ $data->cft_review_by }}
                    @else
                        Not Applicable
                    @endif
                </td>
                <th class="w-20">Pending CFT Review Completed On</th>
                <td class="w-30">
                    @if ($data->cft_review_on)
                        {{ $data->cft_review_on }}
                    @else
                        Not Applicable
                    @endif
                </td>
                <th class="w-20">Pending CFT Review Completed Comment</th>
                <td class="w-30">
                    @if ($data->cft_review_comment)
                        {{ $data->cft_review_comment }}
                    @else
                        Not Applicable
                    @endif
                </td>
            </tr>

            <tr>
                <th class="w-20">Review Completed By</th>
                <td class="w-30">
                    @if ($data->QA_review_completed_by)
                        {{ $data->QA_review_completed_by }}
                    @else
                        Not Applicable
                    @endif
                </td>
                <th class="w-20">Review Completed On</th>
                <td class="w-30">
                    @if ($data->QA_review_completed_on)
                        {{ $data->QA_review_completed_on }}
                    @else
                        Not Applicable
                    @endif
                </td>
                <th class="w-20">Review Completed Comment</th>
                <td class="w-30">
                    @if ($data->QA_review_completed_comment)
                        {{ $data->QA_review_completed_comment }}
                    @else
                        Not Applicable
                    @endif
                </td>
            </tr>

            <tr>
                <th class="w-20">Implemented By</th>
                <td class="w-30">
                    @if ($data->implemented_by)
                        {{ $data->implemented_by }}
                    @else
                        Not Applicable
                    @endif
                </td>
                <th class="w-20">Implemented On</th>
                <td class="w-30">
                    @if ($data->implemented_on)
                        {{ $data->implemented_on }}
                    @else
                        Not Applicable
                    @endif
                </td>

                <th class="w-20">Implemented Comment</th>
                <td class="w-30">
                    @if ($data->implemented_comment)
                        {{ $data->implemented_comment }}
                    @else
                        Not Applicable
                    @endif
                </td>
            </tr>
        </table>
    </div>
    </div>
    </div>

    <footer>
        <table>
            <tr>
                <td class="w-30">
                    <strong>Printed On :</strong> {{ date('d-M-Y') }}
                </td>
                <td class="w-40">
                    <strong>Printed By :</strong> {{ Auth::user()->name }}
                </td>

            </tr>
        </table>
    </footer>

</body>

</html>
