@php $content = $block->content ?? []; $items = $content['items'] ?? []; @endphp
<section class="block-team">
    <div class="team-inner">
        @if(!empty($content['title']))
            <h2 class="block-title">{{ $content['title'] }}</h2>
        @endif
        @if(!empty($content['subtitle']))
            <p class="block-subtitle">{{ $content['subtitle'] }}</p>
        @endif
        <div class="team-grid">
            @forelse($items as $member)
                <div class="team-card">
                    @if(!empty($member['photo']))
                        <div class="team-photo-wrap">
                            <img src="{{ $member['photo'] }}" alt="{{ $member['name'] ?? '' }}" class="team-photo">
                        </div>
                    @else
                        <div class="team-avatar">{{ mb_substr($member['name'] ?? 'A', 0, 1) }}</div>
                    @endif
                    <h3 class="team-name">{{ $member['name'] ?? '' }}</h3>
                    <p class="team-role">{{ $member['role'] ?? '' }}</p>
                    @if(!empty($member['bio'])) <p class="team-bio">{{ $member['bio'] }}</p> @endif
                    <div class="team-social">
                        @if(!empty($member['instagram'])) <a href="{{ $member['instagram'] }}" target="_blank">IG</a> @endif
                        @if(!empty($member['linkedin'])) <a href="{{ $member['linkedin'] }}" target="_blank">LI</a> @endif
                    </div>
                </div>
            @empty
                <p style="text-align:center;color:#999;">Sin miembros configurados</p>
            @endforelse
        </div>
    </div>
</section>
