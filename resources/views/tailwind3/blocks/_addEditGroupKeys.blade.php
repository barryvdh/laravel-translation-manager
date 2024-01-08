<div class="bg-white border border-gray-300 overflow-hidden shadow rounded-lg mt-2">
    <div class="px-4 py-5 sm:p-6">
        <form role="form" method="POST" action="{{action($controller.'@postAddGroup') }}">
            @csrf()
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Choose a group to display the group translations. If no groups are visible, make sure you have run the migrations and imported the translations.</label>
                <select name="group" id="group" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md group-select">
                    @foreach($groups as $key => $value)
                        <option value="{{ $key }}"{{ $key === $group ? ' selected' : ''}}>{{$value}}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Enter a new group name and start edit translations in that group:</label>
                <input type="text" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" name="new-group"/>
            </div>
            <input type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" name="add-group" value="Add and edit keys"/>
        </form>
    </div>
</div>
