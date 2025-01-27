<div class="bg-[url(/imp_data/sampleTemplate.jpg)]
    bg-no-repeat
    bg-cover
    relative
    w-[21.59cm]
    h-[27.94cm]
    ml-auto
    mr-auto
    ">
    @foreach($equipment as $item)
        <p class="text-xs
        capitalize
        pt-16
        my-auto
        ">
        {{ $item->manufacturer }}</p>
    @endforeach
</div>