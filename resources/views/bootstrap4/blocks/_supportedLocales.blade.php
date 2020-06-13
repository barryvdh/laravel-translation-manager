<div class="card mt-2">
    <div class="card-body">
        <fieldset>
            <legend>Supported locales</legend>
            <p>
                Current supported locales:
            </p>
            <form class="form-remove-locale" method="POST" role="form" action="{{action($controller.'@postRemoveLocale')}}"
                  data-confirm="Are you sure to remove this locale and all of data?">
                @csrf()
                <ul class="list-locales list-unstyle">
                    @foreach($locales as $locale)
                        <li class="form-group ">
                            <span>{{$locale}}</span>
                            <button type="submit" name="remove-locale[{{$locale}}]" class="btn btn-danger btn-sm ml-2" data-disable-with="...">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </li>
                    @endforeach
                </ul>
            </form>
            <form class="form-add-locale" method="POST" role="form" action="{{action($controller.'@postAddLocale')}}">
                @csrf()
                <div class="form-group">
                    <p>
                        Enter new locale key:
                    </p>
                    <div class="row">
                        <div class="col-auto">
                            <input type="text" name="new-locale" class="form-control"/>
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn  btn-block btn-outline-success" data-disable-with="Adding..">Add new locale</button>
                        </div>
                    </div>
                </div>
            </form>
        </fieldset>
    </div>
</div>
