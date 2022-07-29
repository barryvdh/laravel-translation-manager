<div class="bg-white border border-gray-300 overflow-hidden shadow rounded-lg my-2">
    <div class="px-4 py-5 sm:p-6">
        <h4 class="font-medium text-xl my-4">Models: {{ $numModelTranslations }}. Total: {{ $numTranslations }}</h4>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" width="15%" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Key</th>
                    <th scope="col" width="15%" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Field</th>
                    @foreach ($locales as $locale)
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $locale }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($translations as $key => $translationModel)
                    @foreach($translationModel as $field => $translation)
                        <tr id="{{ $key }}-{{ $field }}" class="{{ $loop->parent->index % 2 === 0 ? 'bg-white' : 'bg-gray-50' }}">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $key }}</td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $field }}</td>
                            @foreach ($locales as $locale)
                                @php($t = isset($translation[$locale]) ? $translation[$locale] : null)
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                    <a href="#edit"
                                       class="editable status locale-{{ $locale }}"
                                       data-locale="{{ $locale }}" data-name="{{ $locale }}|{{ $field }}|{{ $key }}"
                                       id="username" data-type="textarea" data-pk="{{ $key }}"
                                       data-url="{{ $editUrl }}"
                                       data-title="Enter translation">{{ $t ? htmlentities($t, ENT_QUOTES, 'UTF-8', false) : '' }}</a>
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>
</div>
