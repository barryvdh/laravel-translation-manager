<div class="bg-white border border-gray-300 overflow-hidden shadow rounded-lg mt-2">
    <div class="px-4 py-5 sm:p-6">
        <fieldset>
            <legend class="text-xl mb-2">Export all translations</legend>
            <form class="form-inline form-publish-all" method="POST"
                  action="{{ action($controller.'@postPublish', '*') }}" data-remote="true" role="form"
                  data-confirm="Are you sure you want to publish all translations group? This will overwrite existing language files.">
                @csrf()
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" data-disable-with="Publishing..">Publish all</button>
            </form>
        </fieldset>
    </div>
</div>
