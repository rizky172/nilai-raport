<div class="profile_card">
    <div class="d-flex align-items-center">
        <div>
            <img src="{{ URL::asset('/landing-page/images/profile/profile.png') }}" alt="">
        </div>
        <div class="flex-grow-1">
            <h4 class="mb-0">{{ $row['name'] }}</h4>
            <p class="mb-1">{{ $row['email'] }}</p>
            <p class="mb-0">{{ DateFormat::default(new \DateTime($row['created'])) }}</p>
        </div>
    </div>
    <div class="profile_list">
        <ul class="nav nav-tabs">
        <li><a href="{{ route('account-settings') }}" class="{{ $nav == 'account-settings' ? 'active' : '' }}">My Account<span><i class="la la-angle-right"></i></span></a></li>
            <li><a href="{{ route('account-identities') }}" class="{{ $nav == 'account-identities' ? 'active' : '' }}">My Identities<span><i class="la la-angle-right"></i></span></a></li>
            <li><a href="{{ route('transactions') }}" class="{{ $nav == 'transactions' ? 'active' : '' }}">My Transactions<span><i class="la la-angle-right"></i></span></a></li>
            <li><a href="{{ route('refer') }}" class="{{ $nav == 'refer' ? 'active' : '' }}">My Referral Link<span><i class="la la-angle-right"></i></span></a></li>
        </ul>
    </div>
</div>