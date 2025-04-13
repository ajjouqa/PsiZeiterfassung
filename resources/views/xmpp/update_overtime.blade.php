<div class="modal fade" id="update_overtime{{$summarie->id}}">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content modal-content-demo">
            <div class="modal-header">
                <h6 class="modal-title">User : {{ $username }}</h6><button
                    aria-label="Close" class="close btn" data-dismiss="modal" type="button"><span
                        aria-hidden="true">&times;</span></button>
            </div>
            <form action="{{ route('update.overtime') }}" method="post">
                <div class="modal-body">
                    {{ method_field('put') }}
                    {{ csrf_field() }}
                    <div class="form-group">
                        <h6>Change overtime for Date : {{ $summarie->date->format('Y-m-d') }}</h6>                        
                        <div>
                            <label for="overtime"></label>
                            <input type="number" name="overtime" class="form-control" value="{{ $summarie->over_time }}" id="overtime" placeholder="Enter Overtime" >
                        </div>
                        <input type="hidden" name="summarie_id" value="{{ $summarie->id }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn ripple btn-primary"
                        type="submit">Update</button>
                    <button class="btn ripple btn-secondary" data-dismiss="modal"
                        type="button">close</button>
                </div>
            </form>
        </div>
    </div>
</div>
