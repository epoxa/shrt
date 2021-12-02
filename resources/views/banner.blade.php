<x-layout :title="'Ждите ...'">
    <script>
        let delay = 5;
        const header = document.getElementsByTagName('h1')[0];
        function tick() {
            if (!delay) {
                header.innerHTML = 'Поехали!';
                location = '{{ $url }}';
            } else {
                header.innerHTML = `Ждите &nbsp;<span class="text-danger"> ${delay} </span>&nbsp; сек.`;
                delay--;
                setTimeout(tick, 1000)
            }
        }
    </script>
    <img src="{{ $banner_url }}" onload="tick()">
</x-layout>
