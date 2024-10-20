<div class="modal-body">

    @foreach (json_decode($surveys->report) as $report)
        <div class="row border-bottom mb-2 pb-2">
            <h5 class="mb-3 text-primary">Report {{$loop->iteration}} <small class="text-secondary">({{ \Carbon\Carbon::parse($report->date)->format('d F, Y') }})</small></h5>
            <h6>File: <a href="{{ url('storage/survey/'.$report->file) }}" class="btn btn-primary btn-sm mx-2" download>
                <i class="ti ti-download text-white"></i>
            </a>
            </h6>
            <h6>Description: {{$report->description}}</h6>
        </div>
    @endforeach

    @if (count(json_decode($surveys->report)) == 0)
       <div class="row text-center">
            <h6>No Report Found!</h6>
       </div>
    @endif

</div>

<div class="modal-footer border-0">
    <input type="button" value="{{__('Close')}}" class="btn btn-primary" data-bs-dismiss="modal">
</div>
