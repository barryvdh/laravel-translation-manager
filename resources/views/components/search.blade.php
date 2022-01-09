<fieldset>
    <legend>Search</legend>
    <form role="form" method="get" action="{{ route('translation-manager.search') }}">
        <div class="form-group">
            <p>Search for translation text</p>
            <div class="input-group">
            <input type="text" class="form-control" name="q" value="{{ $q ?? "" }}"/>
            <div class="input-group-btn">
                <button type="submit" class="btn btn-default"><span class="glyphicon glyphicon-search"></span> Search</button>
            </div>
            </div>
        </div>
    </form>
</fieldset>