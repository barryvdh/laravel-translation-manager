@if(!empty($models))
    <div class="card mt-2">
        <div class="card-body">
            <div class="form-group">
                <p>Choose a model to display the translations.</p>
                <select name="model" id="model" class="form-control model-select">
                    @foreach($models as $key => $value)
                        <option value="{{ $key }}"{{ $key === $selectedModel ? ' selected' : ''}}>{{$value}}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
@endif
