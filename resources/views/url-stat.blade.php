<x-layout :title="request()->root() . '/' . $url->short_url">
    <dl class="row">
        <dt class="col-sm-3">Тип ссылки</dt>
        <dd class="col-sm-9">@if($url->is_commercial) Коммерческая @else Свободная @endif</dd>

        <dt class="col-sm-3">Срок действия</dt>
        <dd class="col-sm-9">{!! $time_remains !!}</dd>

        <dt class="col-sm-3">Переходов</dt>
        <dd class="col-sm-9">@if($clicks_total) Всего {{ $clicks_total }} (посетителей: {{ $ips_total }}) @else Пока нет @endif</dd>
    </dl>
    @if ($clicks->total())
        <div class="d-flex justify-content-end">
            {{ $clicks->links() }}
        </div>
        <table class="table table-sm">
            <thead>
            <tr>
                <th scope="col">Время</th>
                <th scope="col">IP</th>
                @if($url->is_commercial)
                    <th scope="col">Баннер</th>
                @endif
            </tr>
            </thead>
            <tbody class="table-hover">
            @foreach($clicks as $click)
                <tr>
                    <td>{{ $click->created_at }}</td>
                    <td>{{ $click->ip }}</td>
                    @if($url->is_commercial)
                        <td>
                            <a href="javascript:void(0)" class="popover-img" data-trigger="hover" data-placement="left"
                               data-content="<img src='{{ request()->root() }}/banners/{{ $click->banner->file_name }}' width=80>"
                               data-html="true">
                                {{ $click->banner->file_name }}
                            </a>
                        </td>
                    @endif
                </tr>
            @endforeach
            </tbody>
        </table>
    @else
        <div class="d-flex justify-content-center">
            <a class="btn btn-secondary" href="{{ request()->root() . "/" . $url->short_url }}">Переход</a>
        </div>
        <div class="alert alert-light">
            Сохраните эту страницу в закладки, чтобы смотреть
            статистику переходов по этой ссылке.
        </div>
    @endif

    @if($url->is_commercial)
        <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-fQybjgWLrvvRgtW6bFlB7jaZrFsaBXjsOMm/tB9LTS58ONXgqbR9W8oWht/amnpF" crossorigin="anonymous"></script>
        <script>
            $(function () {
                $('.popover-img').popover({
                    container: 'body'
                })
            })
        </script>
    @endif
</x-layout>
