<div class="card-body">
    <div class="tab-content" id="myTabContent2">
        <div class="tab-pane fade show active" id="item" role="tabpanel" aria-labelledby="profile-tab3">
            <div class="timeline d-flex align-items-center flex-column">
                @php
                    $levelCounter = 1;
                @endphp

                @foreach ($approvals as $approval)
                    @if ($approval->module === 'recruitment')
                        <div class="timeline-item">
                            <div class="timeline-content">
                                <h5 class="mb-0">Layer {{ $levelCounter }} - {{ $approval->role->name }}</h5>
                            </div>
                            <div class="actions">
                                <a href="{{ route('recruitment.approval.delete', $approval->id) }}"
                                    class="btn btn-sm btn-danger"><i class="fas fa-trash-alt"></i></a>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <h6 class="mb-0">↓</h6>
                        </div>
                        @php
                            $levelCounter++;
                        @endphp
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    <div class="timeline-item justify-content-center">
        <button class="btn btn-primary btn-sm" id="addStepBtn" data-bs-toggle="modal" data-bs-target="#recruitmentModal">
            <i class="fa fa-plus"></i>
        </button>
        <div class="modal fade" id="recruitmentModal" tabindex="-1" aria-labelledby="recruitmentModalLabel" aria-hidden="true"
            data-bs-backdrop="static">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="recruitmentModalLabel">Select Role
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('recruitment.approval.store') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3 d-flex align-items-center">
                                <select class="form-select" name="role_id" required>
                                    <option value="" selected disabled>Select
                                        a role</option>
                                    @foreach ($roles as $role)
                                        @php
                                            $addedRole = $approvals
                                                ->where('module', 'recruitment')
                                                ->where('role_id', $role->id)
                                                ->first();
                                        @endphp
                                        @if (!$addedRole)
                                            <option value="{{ $role->id }}">
                                                {{ $role->name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-primary ms-3" id="submitStepBtn">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
