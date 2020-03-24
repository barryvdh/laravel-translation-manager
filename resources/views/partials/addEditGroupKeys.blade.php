<div class="card mt-2">
    <div class="card-body">
        <form role="form" method="POST" action="{{action($controller.'@postAddGroup') }}">
            @csrf()
            <div class="form-group">
                <p>{{ trans('manager::translations.chooseGroupHint') }}</p>
                <select name="group" id="group" class="form-control group-select">
                    @foreach($groups as $key => $value)
                    <option value="{{$key}}"{{ $key === $group ? ' selected' : ''}}>@translationGroupName($value)</option>
                    @endforeach
                </select>
            </div>
            @if (config('translation-manager.blade.add_group_enabled', true))
            <div class="form-group">
                <label>{{ trans('manager::translations.enterNewGroupName') }}</label>
                <input type="text" class="form-control" name="new-group"/>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" name="add-group" value="Add and edit keys"/>
            </div>
            @endif
        </form>
    </div>
</div>
