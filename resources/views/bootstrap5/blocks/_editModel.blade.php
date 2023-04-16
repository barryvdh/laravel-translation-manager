<div class="card mt-2 mb-4">
<div class="card-body">
        <h4>Models: {{ $numModelTranslations }}. Total: {{ $numTranslations }}</h4>
        <table class="table">
            <thead>
                <tr>
                    <th scope="col" width="15%">Key</th>
                    <th scope="col" width="15%">Field</th>
                    @foreach ($locales as $locale)
                        <th scope="col">{{ $locale }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($translations as $key => $translationModel)
                    @foreach($translationModel as $field => $translation)
                        <tr id="{{ $key }}-{{ $field }}">
                            <td>{{ $key }}</td>
                            <td>{{ $field }}</td>
                            @foreach ($locales as $locale)
                                @php($t = isset($translation[$locale]) ? $translation[$locale] : null)
                                <td>
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
