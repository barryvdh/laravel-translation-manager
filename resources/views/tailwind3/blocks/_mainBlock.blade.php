<div class="bg-white border border-gray-300 overflow-hidden shadow rounded-lg mt-2">
    <div class="px-4 py-5 sm:p-6">
        <p class="block text-sm text-gray-700">Warning, translations are not visible until they are exported back to the app/lang file, using <code class="text-sm text-orange-500">php artisan translation:export</code> command or publish button.</p>

        @if(!isset($group))
            <form class="form-import" method="POST" action="{{ action($controller . '@postImport') }}" data-remote="true" role="form" aria-label="Import">
                @csrf()
                <div class="my-6 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                    <div class="sm:col-span-3">
                        <select name="replace" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                            <option value="0">Append new translations</option>
                            <option value="1">Replace existing translations</option>
                        </select>
                    </div>
                    <div class="sm:col-span-3">
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" data-disable-with="Loading...">Import groups</button>
                    </div>
                </div>
            </form>
            <form class="form-find" method="POST" action="{{ action($controller.'@postFind') }}" data-remote="true" role="form"
                  data-confirm="Are you sure you want to scan you app folder? All found translation keys will be added to the database."
                  aria-label="Scan folders"
            >
                @csrf()
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-cyan-600 hover:bg-cyan-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500" data-disable-with="Searching...">Find translations in files</button>
            </form>
        @else
            <form class="form-inline form-publish" method="POST" action="{{ action($controller.'@postPublish', $group) }}" data-remote="true" role="form"
                  data-confirm="Are you sure you want to publish the translations group '{{ $group }}'? This will overwrite existing language files."
                  aria-label="Publish"
            >
                @csrf()
                <div class="mt-6" role="group">
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-cyan-600 hover:bg-cyan-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500" data-disable-with="Publishing...">Publish translations</button>
                    <a href="{{ action($controller.'@getIndex') }}" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">Back</a>
                </div>
            </form>
        @endif
    </div>
</div>
