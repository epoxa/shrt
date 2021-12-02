<x-layout :title="'За 2 недели'">
    <dl class="row">
        <dt class="col-sm-5">Ссылок использовано</dt>
        <dd class="col-sm-7">{{ $urls_total }}</dd>

        <dt class="col-sm-5">Уникальных посетителей</dt>
        <dd class="col-sm-7">{{ $ips_total }}</dd>

        <dt class="col-sm-5">Переходов всего</dt>
        <dd class="col-sm-7">{{ $clicks_total }}</dd>

        <dt class="col-sm-5">Уникальных переходов</dt>
        <dd class="col-sm-7">{{ $clicks_unique }}</dd>
    </dl>
</x-layout>
