<x-layout>
    <form action="/save">
        @csrf
        <div class="form-group">
            <label for="original_url">
                Ваша ссылка
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon3">http://</span>
                    </div>
                    <input type="text" name="original_url" id="original_url" class="form-control"
                           value="{{$url->original_url}}">
                </div>
            </label>
            @if (isset($bugs['original_url']))
                <div class="text-danger">
                    {{ $bugs['original_url'][0] }}
                </div>
            @endif
        </div>
        <div class="form-group">
            <label for="is_commercial">
                <input type="checkbox" name="is_commercial" id="is_commercial" class="checkbox"
                       @if ($url->is_commercial) checked @endif>
                Коммерческая
            </label>
        </div>
        <div class="form-group">
            <label for="valid_until">
                Срок действия
                <input type="datetime-local" name="valid_until" id="valid_until"
                       class="form-control"
                       value="{{$url->valid_until}}">
            </label>
        </div>
        <div class="form-group">
            <label for="original_url">
                Короткая ссылка
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon3">{{request()->root()}}/</span>
                    </div>
                    <input type="text" name="short_url" id="short_url" class="form-control"
                           value="{{$url->short_url}}">
                </div>
            </label>
            @if (isset($bugs['short_url']))
                <div class="text-danger">
                    {{ $bugs['short_url'][0] }}
                </div>
            @endif
        </div>
        <div class="form-group align-right">
            <button type="submit" class="form-control">Укротить</button>
        </div>
        <div class="form-group">
        </div>
    </form>
</x-layout>
