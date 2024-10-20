<div class="modal-body">
    <div class="row border-bottom pb-1">
        <h5 class="mb-3">Attendees</h5>
        @foreach ($attendees as $attendee)
            <div class="d-flex align-items-center mb-2">
                <div>
                    <img @if($attendee['profile']) src="{{$attendee['profile']}}" @else src="{{asset('/storage/uploads/avatar/avatar.png')}}" @endif class="wid-30 rounded-circle me-3" alt="avatar image">
                </div>
                <p class="mb-0">{{$attendee['name']}}</p>
            </div>
        @endforeach
    </div>

    <div class="row border-bottom mb-2">
        <h5 class="mt-3">Topic:</h5>
        <p>{{$mom->topic}}</p>
    </div>

    <div class="row border-bottom mb-2">
        <h5 class="mt-1">Place and Time:</h5>
        <p>{{$mom->place_time}}</p>
    </div>

    @foreach ($interactions as $index => $interaction)
        <div class="row border-bottom mb-2">
            <h5 class="mt-1">Client Interaction {{$index+1}}:</h5>
            <p>{{$interaction}}</p>
        </div>
    @endforeach

    @if ($mom->next_plan)
        <div class="row border-bottom pb-2">
            <h5 class="mt-1">Next Action: <span class="text-primary">{{\App\Models\Department::find($mom->next_plan)->name}} Department</span></h5>
        </div>
    @endif

</div>

<div class="modal-footer border-0">
    <input type="button" value="{{__('Close')}}" class="btn btn-primary" data-bs-dismiss="modal">
</div>
