<div class="modal fade" id="RequestAModifcation{{$summarie->id}}">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content modal-content-demo">
            <div class="modal-header">
                <h6 class="modal-title">User : {{ $username }}</h6><button aria-label="Close" class="close btn"
                    data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
            </div>
            <form action="{{ route('request.a.modification.azubi') }}" method="post">
                <div class="modal-body">
                    {{ method_field('post') }}
                    {{ csrf_field() }}
                    <div class="form-group">
                        <h4>Reques an update </h4>
                        <div>
                            <label for="password"></label>
                            <select name="requested_status" id="" class="form-control">
                                <option value="">Select Status ur</option>
                                <option value="working" @if ($summarie->status && $summarie->status->status == 'working')
                                selected @endif>Working</option>
                                <option value="sick" @if ($summarie->status && $summarie->status->status == 'sick')
                                selected @endif>Sick</option>
                                <option value="school" @if ($summarie->status && $summarie->status->status == 'school')
                                selected @endif>School</option>
                                <option value="off" @if ($summarie->status && $summarie->status->status == 'off') selected
                                @endif>off</option>
                            </select>
                        </div>
                        <div>
                            <label for="comment"></label>
                            <textarea name="reason" id="" class="form-control" rows="5"
                                placeholder="Type your reason here..."></textarea>
                        </div>
                        <input type="hidden" name="date" value="{{ $summarie->date->format('Y-m-d') }}">
                        <input type="hidden" name="summarie_id" value="{{ $summarie->id }}">
                        
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn ripple btn-primary" type="submit">Send</button>
                    <button class="btn ripple btn-secondary" data-dismiss="modal" type="button">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>