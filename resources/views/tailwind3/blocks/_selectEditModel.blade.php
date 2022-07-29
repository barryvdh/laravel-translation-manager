@if(!empty($models))
    <div class="bg-white border border-gray-300 overflow-hidden shadow rounded-lg mt-2">
        <div class="px-4 py-5 sm:p-6">
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Choose a model to display the translations.</label>
                <select name="model" id="model" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md model-select">
                    @foreach($models as $key => $value)
                        <option value="{{ $key }}"{{ $key === $selectedModel ? ' selected' : ''}}>{{$value}}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
@endif
