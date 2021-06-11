<form class="form-inline form-publish" method="POST" action="{{ action('\Barryvdh\TranslationManager\Controller@postPublish', $group) }}" data-remote="true" role="form" data-confirm="Are you sure you want to publish the translations group '{{ $group }}? This will overwrite existing language files.">
    @csrf
    <button type="submit" class="btn btn-info" data-disable-with="Publishing.." >Publish translations</button>
    <a href="{{ route('translation-manager.index')  }}" class="btn btn-default">Back</a>
</form>