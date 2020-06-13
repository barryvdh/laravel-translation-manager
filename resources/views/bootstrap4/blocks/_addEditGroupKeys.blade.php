<div class="card mt-2">
    <div class="card-body">
        <form role="form" method="POST" action="{{action($controller.'@postAddGroup') }}">
            @csrf()
            <div class="form-group">
                <p>Choose a group to display the group translations. If no groups are visisble, make sure you have run the migrations and imported the translations.</p>
                <select name="group" id="group" class="form-control group-select">
                    @foreach($groups as $key => $value)
                        <option value="{{$key}}"{{ $key == $group ? ' selected' : ''}}>{{$value}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Enter a new group name and start edit translations in that group</label>
                <input type="text" class="form-control" name="new-group"/>
            </div>
            <div class="form-group">
                    <input type="submit" class="btn btn-primary" name="add-group" value="Add and edit keys"/>
            </div>
        </form>
    </div>
</div>
