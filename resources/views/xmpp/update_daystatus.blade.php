<div class="modal fade" id="update_daystatus{{$summarie->id}}">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content modal-content-demo">
            <div class="modal-header">
                <h6 class="modal-title">User : {{ $username }}</h6><button
                    aria-label="Close" class="close btn" data-dismiss="modal" type="button"><span
                        aria-hidden="true">&times;</span></button>
            </div>
            <form action="{{ route('update.status') }}" method="post">
                <div class="modal-body">
                    {{ method_field('put') }}
                    {{ csrf_field() }}
                    <div class="form-group">
                        <h4>Update Status day for  date : {{ $summarie->date->format('Y-m-d') }}</h4>
                        <div>
                            <label for="password"></label>
                            <select name="status" id="" class="form-control">
                                <option value="">Select Status</option>
                                <option value="working" @if ($summarie->status && $summarie->status->status == 'working') selected @endif>Working</option>
                                <option value="sick" @if ($summarie->status && $summarie->status->status == 'sick') selected @endif>Sick</option>
                                <option value="school" @if ($summarie->status && $summarie->status->status == 'school') selected @endif>School</option>
                                <option value="off" @if ($summarie->status && $summarie->status->status == 'off') selected @endif>off</option>
                            </select>
                        </div>
                        <div>
                            <label for="comment"></label>
                            <textarea name="notes" id="" class="form-control" rows="5">
                                @if ($summarie->status)
                                    {{ $summarie->status->notes }}
                                @else
                                    Not Comment
                                @endif
                            </textarea>
                        </div>
                        <input type="hidden" name="summary_id" value="{{ $summarie->id }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn ripple btn-primary"
                        type="submit">Update</button>
                    <button class="btn ripple btn-secondary" data-dismiss="modal"
                        type="button">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
