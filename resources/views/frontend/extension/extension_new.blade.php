@extends('frontend.layout.main')
@section('container')
    @php
        $users = DB::table('users')->select('id', 'name', 'role')->where('active', 1)->get();
        $userRoles = DB::table('user_roles')->select('user_id')->where('q_m_s_roles_id', 4)->distinct()->get();
        $departments = DB::table('departments')->select('id', 'name')->get();
        $divisions = DB::table('q_m_s_divisions')->select('id', 'name')->get();

        $userIds = DB::table('user_roles')
            ->where('q_m_s_roles_id', 4)
            ->distinct()
            ->pluck('user_id');

        // Step 3: Use the plucked user_id values to get the names from the users table
        $userNames = DB::table('users')
            ->whereIn('id', $userIds)
            ->pluck('name');

        // If you need both id and name, use the select method and get
        $userDetails = DB::table('users')
            ->whereIn('id', $userIds)
            ->select('id', 'name')
            ->get();
        // dd ($userIds,$userNames, $userDetails);
    @endphp
    <style>
        textarea.note-codable {
            display: none !important;
        }

        header {
            display: none;
        }
       
        
    </style>
    </style>

    <script>
        $(document).ready(function() {
            $('#ObservationAdd').click(function(e) {
                function generateTableRow(serialNumber) {

                    var html =
                        '<tr>' +
                        '<td><input disabled type="text" name="jobResponsibilities[' + serialNumber +
                        '][serial]" value="' + serialNumber +
                        '"></td>' +
                        '<td><input type="text" name="jobResponsibilities[' + serialNumber +
                        '][job]"></td>' +
                        '<td><input type="text" class="Document_Remarks" name="jobResponsibilities[' +
                        serialNumber + '][remarks]"></td>' +


                        '</tr>';

                    return html;
                }

                var tableBody = $('#job-responsibilty-table tbody');
                var rowCount = tableBody.children('tr').length;
                var newRow = generateTableRow(rowCount + 1);
                tableBody.append(newRow);
            });
        });
    </script>
    <div class="form-field-head">

        <div class="division-bar">
            <strong>Site Division/Project</strong> :
            {{ Helpers::getDivisionName(session()->get('division')) }} / 
            {{-- {{ Helpers::getDivisionName($data->division_id) }} / --}}
            Extension
        </div>
    </div>





    {{-- ======================================
                    DATA FIELDS
    ======================================= --}}
    <div id="change-control-fields">
        <div class="container-fluid">
        
            <!-- Tab links -->
            <div class="cctab">

                <button class="cctablinks active" onclick="openCity(event, 'CCForm1')">General Information</button>
                <button class="cctablinks " onclick="openCity(event, 'CCForm2')">HOD Review</button>
                <button class="cctablinks " onclick="openCity(event, 'CCForm3')">QA Approval</button>

                <button class="cctablinks" onclick="openCity(event, 'CCForm6')">Activity Log</button>

            </div>
            <form action="{{ route('extension_new.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <!-- Tab content -->
            <div id="step-form">
                <div id="CCForm1" class="inner-block cctabcontent">
                    <div class="inner-block-content">
                        <div class="row">
                        @if (!empty($parent_id))
                                    <input type="hidden" name="parent_id" value="{{ $parent_id }}">
                                    <input type="hidden" name="parent_type" value="{{ $parent_type }}">
                                    <input type="hidden" name="parent_record" id="parent_record" value="{{ $parent_record }}">
                                @endif
                        <div class="col-lg-6">
                            <div class="group-input">
                                <label for="RLS Record Number"><b>Record Number</b></label>
                                <input disabled type="text" name="record_number"
                                value="{{ Helpers::getDivisionName($parentDivisionId) }}/Ext/{{ date('y') }}/{{ $record_number }}">
                                {{-- <div class="static">QMS-EMEA/CAPA/{{ date('Y') }}/{{ $record_number }}</div> --}}
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="group-input">
                                <label for="Division Code"><b>Site/Location Code</b></label>
                                <input readonly type="text" value="{{ Helpers::getDivisionName($parentDivisionId) }}"> 
                                <input type="hidden" name="site_location_code" value="{{ $parentDivisionId }}">
                            </div>
                        </div>
                        
                        <div class="col-lg-6">
                            <div class="group-input">
                                <label for="Initiator"><b>Initiator</b></label>
                                <input disabled type="text" value="{{ Auth::user()->name }}">

                            </div>
                        </div>


                        @php
                            // Calculate the due date (30 days from the initiation date)
                            $initiationDate = date('Y-m-d'); // Current date as initiation date
                            $dueDate = date('Y-m-d', strtotime($initiationDate . '+30 days')); // Due date
                        @endphp

                        <div class="col-lg-6">
                            <div class="group-input">
                                <label for="Date of Initiation"><b>Date of Initiation</b></label>
                                <input readonly type="text" value="{{ date('d-M-Y') }}" name="initiation_date_new"
                                    id="initiation_date"
                                    style="background-color: light-dark(rgba(239, 239, 239, 0.3), rgba(59, 59, 59, 0.3))">
                                <input type="hidden" value="{{ date('Y-m-d') }}" name="initiation_date">
                            </div>
                        </div>
                            
                        <div class="col-12">
                            <div class="group-input">
                                <label for="short_description">Short Description <span class="text-danger">*</span></label>
                                <span id="rchars">255</span> Characters remaining
                                <div style="position:relative;">
                                    <input 
                                        id="short_description" 
                                        type="text" 
                                        name="short_description" 
                                        maxlength="255"
                                        required
                                        class="mic-input"
                                    >
                                    <button class="mic-btn" type="button">
                                        <i class="fas fa-microphone"></i>
                                    </button>
                                </div>
                            </div>
                            {{-- @error('short_description')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror --}}
                        </div>
                        
                            <script>
                                var maxLength = 255;
                                $('#docname').keyup(function() {
                                    var textlen = maxLength - $(this).val().length;
                                    $('#rchars').text(textlen);});
                            </script>
                            <div class="col-lg-6">
                                <div class="group-input">
                                    <label for="Assigned To">HOD review  </label>
                                    <select id="choices-multiple-remove" class="choices-multiple-reviewe"
                                        name="reviewers" placeholder="Select Reviewers"  >
                                        <option value="">-- Select --</option>
                                        @if (!empty($users))
                                        
                                            @foreach ($users as $lan)
                                                @if(Helpers::checkUserRolesreviewer($lan))
                                                    <option value="{{ $lan->id }}">
                                                        {{ $lan->name }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-lg-6">
                                <div class="group-input">
                                    <label for="Assigned To">QA approval </label>
                                    <select id="choices-multiple-remove-but" class="choices-multiple-reviewer"
                                        name="approvers" placeholder="Select Approvers" >
                                        <option value="">-- Select --</option>

                                        @if (!empty($users))
                                            @foreach ($users as $lan)
                                                @if(Helpers::checkUserRolesApprovers($lan))
                                                    <option value="{{ $lan->id }}">
                                                        {{ $lan->name }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                           
                            <div class="col-lg-6 new-date-data-field">
                                <div class="group-input input-date">
                                    <label for="Actual Start Date">Current Due Date (Parent)</label>
                                    <div class="calenderauditee">
                                        <input type="text" id="start_date" readonly placeholder="DD-MMM-YYYY" />
                                        <input type="date" name="current_due_date"
                                            min="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" value=""
                                            class="hide-input" oninput="handleDateInput(this, 'start_date')" />
                                    </div>
                                </div>
                            </div>
                           
                            <div class="col-lg-6 new-date-data-field">
                                <div class="group-input input-date">
                                    <label for="Actual Start Date">Proposed Due Date</label>
                                    <div class="calenderauditee">
                                        <input type="text" id="test_date" readonly placeholder="DD-MMM-YYYY" />
                                        <input type="date" name="proposed_due_date"
                                            min="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" value=""
                                            class="hide-input" oninput="handleDateInput(this, 'test_date')" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="group-input">
                                    <label for="description">Description</label>
                                    <div style="position:relative;">
                                        <textarea name="description" id="description" cols="30" class="mic-input"></textarea>
                                        <button class="mic-btn" type="button">
                                            <i class="fas fa-microphone"></i>
                                        </button>
                                    </div>
                                </div>
                                {{-- @error('short_description')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror --}}
                            </div>
                            
                            <div class="col-12">
                                <div class="group-input">
                                    <label for="justification_reason">Justification / Reason</label>
                                    <div style="position:relative;">
                                        <textarea name="justification_reason" id="justification_reason" cols="30" class="mic-input"></textarea>
                                        <button class="mic-btn" type="button">
                                            <i class="fas fa-microphone"></i>
                                        </button>
                                    </div>
                                </div>
                                {{-- @error('short_description')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror --}}
                            </div>
                            
                            <div class="col-12">
                                <div class="group-input">
                                    <label for="Guideline Attachment"> Attachment Extension </label>
                                    <div><small class="text-primary">Please Attach all relevant or supporting
                                            documents</small></div>
                                    <div class="file-attachment-field">
                                        <div class="file-attachment-list" id="file_attachment_extension"></div>
                                        <div class="add-btn">
                                            <div>Add</div>
                                            <input type="file" id="myfile" name="file_attachment_extension[]"
                                                oninput="addMultipleFiles(this, 'file_attachment_extension')" multiple>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="button-block">
                            <button type="submit" id="ChangesaveButton01" class="saveButton">Save</button>
                            <button type="button" id="ChangeNextButton" class="nextButton">Next</button>
                            <button type="button"> <a href="{{ url('TMS') }}" class="text-white">
                                    Exit </a> </button>
                        </div>

                    </div>
                </div>
            </div>

            <!-- reviewer content -->
            <div id="CCForm2" class="inner-block cctabcontent">
                <div class="inner-block-content">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="group-input">
                                <label for="reviewer_remarks">HOD Remarks</label>
                                <div style="position:relative;">
                                    <textarea name="reviewer_remarks" id="reviewer_remarks" cols="30" class="mic-input"></textarea>
                                    <button class="mic-btn" type="button">
                                        <i class="fas fa-microphone"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                       
                        <div class="col-12">
                            <div class="group-input">
                                <label for="Guideline Attachment">HOD Attachment  </label>
                                <div><small class="text-primary">Please Attach all relevant or supporting
                                        documents</small></div>
                                <div class="file-attachment-field">
                                    <div class="file-attachment-list" id="file_attachment_reviewer"></div>
                                    <div class="add-btn">
                                        <div>Add</div>
                                        <input type="file" id="myfile" name="file_attachment_reviewer[]"
                                            oninput="addMultipleFiles(this, 'file_attachment_reviewer')" multiple>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="button-block">
                        <button type="submit" id="ChangesaveButton02" class="saveButton">Save</button>
                        <button type="button" id="ChangeNextButton" class="nextButton">Next</button>
                        <button type="button"> <a href="{{ url('TMS') }}" class="text-white">
                                Exit </a> </button>
                    </div>
                </div>
            </div>
            <!-- Approver-->
            <div id="CCForm3" class="inner-block cctabcontent">
                <div class="inner-block-content">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="group-input">
                                <label for="approver_remarks">QA Remarks</label>
                                <div style="position:relative;">
                                    <textarea name="approver_remarks" id="approver_remarks" cols="30" class="mic-input"></textarea>
                                    <button class="mic-btn" type="button">
                                        <i class="fas fa-microphone"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                       
                        <div class="col-12">
                            <div class="group-input">
                                <label for="Guideline Attachment">QA Attachment  </label>
                                <div><small class="text-primary">Please Attach all relevant or supporting
                                        documents</small></div>
                                <div class="file-attachment-field">
                                    <div class="file-attachment-list" id="file_attachment_approver"></div>
                                    <div class="add-btn">
                                        <div>Add</div>
                                        <input type="file" id="myfile" name="file_attachment_approver[]"
                                            oninput="addMultipleFiles(this, 'file_attachment_approver')" multiple>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="button-block">
                        <button type="submit" id="ChangesaveButton02" class="saveButton">Save</button>
                        <button type="button" id="ChangeNextButton" class="nextButton">Next</button>
                        <button type="button"> <a href="{{ url('TMS') }}" class="text-white">
                                Exit </a> </button>
                    </div>
                </div>
            </div>
             <!-- Activity Log content -->
             <div id="CCForm6" class="inner-block cctabcontent">
                <div class="inner-block-content">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="group-input">
                                <label for="Activated By">Submited By</label>
                                <div class="static"></div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="group-input">
                                <label for="Activated On">Submited On</label>
                                <div class="static"></div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="group-input">
                                <label for=" Rejected By">Reviewed By</label>
                                <div class="static"></div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="group-input">
                                <label for="Rejected On">Reviewed On</label>
                                <div class="static"></div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="group-input">
                                <label for=" Rejected By">More Info Required By</label>
                                <div class="static"></div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="group-input">
                                <label for="Rejected On">More Info Required  On</label>
                                <div class="static"></div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="group-input">
                                <label for=" Rejected By">Approved By</label>
                                <div class="static"></div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="group-input">
                                <label for="Rejected On">Approved On</label>
                                <div class="static"></div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="group-input">
                                <label for=" Rejected By">More Info Required By</label>
                                <div class="static"></div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="group-input">
                                <label for="Rejected On">More Info Required  On</label>
                                <div class="static"></div>
                            </div>
                        </div>

                    </div>
                    {{-- <div class="button-block">
                        <button type="submit" class="saveButton">Save</button>
                        <a href="/rcms/qms-dashboard">
                            <button type="button" class="backButton">Back</button>
                        </a>
                        <button type="submit">Submit</button>
                        <button type="button"> <a href="{{ url('rcms/qms-dashboard') }}" class="text-white">
                                Exit </a> </button>
                    </div> --}}
                </div>
            </div>
            </form>
        </div>
    </div>

    <script>
        function openCity(evt, cityName) {
            var i, cctabcontent, cctablinks;
            cctabcontent = document.getElementsByClassName("cctabcontent");
            for (i = 0; i < cctabcontent.length; i++) {
                cctabcontent[i].style.display = "none";
            }
            cctablinks = document.getElementsByClassName("cctablinks");
            for (i = 0; i < cctablinks.length; i++) {
                cctablinks[i].className = cctablinks[i].className.replace(" active", "");
            }
            document.getElementById(cityName).style.display = "block";
            evt.currentTarget.className += " active";
        }

        const saveButtons = document.querySelectorAll('.saveButton1');
        const form = document.getElementById('step-form');
    </script>
    <script>
        VirtualSelect.init({
            ele: '#Facility, #Group, #Audit, #Auditee ,#reference_record, #designee, #hod'
        });
    </script>

<style>
    .mic-btn {
        background: none;
        border: none;
        outline: none;
        cursor: pointer;
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        box-shadow: none;
        color: black;
        display: none;
        /* Hide the button initially */
    }

    .relative-container textarea {
        width: 100%;
        padding-right: 40px;
    }

    .relative-container input:focus+.mic-btn {
        display: inline-block;
        /* Show the button when input is focused */
    }

    .mic-btn:focus,
    .mic-btn:hover,
    .mic-btn:active {
        box-shadow: none;
    }
</style>

<script>
    < link rel = "stylesheet"
    href = "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" >
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const recognition = new(window.SpeechRecognition || window.webkitSpeechRecognition)();
        recognition.continuous = false;
        recognition.interimResults = false;
        recognition.lang = 'en-US';

        function startRecognition(targetElement) {
            recognition.start();
            recognition.onresult = function(event) {
                const transcript = event.results[0][0].transcript;
                targetElement.value += transcript;
            };
            recognition.onerror = function(event) {
                console.error(event.error);
            };
        }

        document.addEventListener('click', function(event) {
            if (event.target.closest('.mic-btn')) {
                const button = event.target.closest('.mic-btn');
                const inputField = button.previousElementSibling;
                if (inputField && inputField.classList.contains('mic-input')) {
                    startRecognition(inputField);
                }
            }
        });

        document.querySelectorAll('.mic-input').forEach(input => {
            input.addEventListener('focus', function() {
                const micBtn = this.nextElementSibling;
                if (micBtn && micBtn.classList.contains('mic-btn')) {
                    micBtn.style.display = 'inline-block';
                }
            });

            input.addEventListener('blur', function() {
                const micBtn = this.nextElementSibling;
                if (micBtn && micBtn.classList.contains('mic-btn')) {
                    setTimeout(() => {
                        micBtn.style.display = 'none';
                    }, 200); // Delay to prevent button from hiding immediately when clicked
                }
            });
        });
    });
</script>
@endsection
