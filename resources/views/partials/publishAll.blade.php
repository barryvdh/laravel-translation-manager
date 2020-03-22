<div class="card mt-2">
    <div class="card-body">
        <fieldset>
            <legend>Export all translations</legend>
            <form class="form-inline form-publish-all" method="POST" action="{{action($controller.'@postPublish', '*') }}" data-remote="true" role="form"
                  data-confirm="Are you sure you want to publish all translations group? This will overwrite existing language files.">
                    @csrf()
                <button type="submit" class="btn btn-primary" data-disable-with="Publishing..">Publish all</button>
            </form>
        </fieldset>
    </div>
</div>
