<div class="game_skin-card">
    @if ($first)
    <p class="ribbon">
        <span>Hot</span>
    </p>
    @endif

    <h5>
        <span class="price-cut ">{{ $giftcard['price'] }}</span> {{ round($giftcard['price'] * 0.7) }} <img src="/images/gem.svg" alt="">
    </h5>

    <div class="game_skin-img">
        <img src="{{ $giftcard['image'] }}" alt="" class="img-fluid">
    </div>
    <div class="game_skin-info">
        <div class="game_skin-text">
            <span>{{ $giftcard['category'] }}</span>
            <h6>{{ $giftcard['name'] }}</h6>
        </div>

        <a href="{{ route('gift-card-detail', $giftcard['id']) }}">
            <span><i class="fa fa-angle-right"></i></span>
        </a>
    </div>
</div>