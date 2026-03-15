<div class="skeleton-page">
    {{-- Stat cards --}}
    <div class="stat-grid" style="margin-bottom:20px;">
        @foreach(range(1, $cards ?? 4) as $i)
        <div class="skeleton-card" style="height:90px;"></div>
        @endforeach
    </div>

    {{-- Content blocks --}}
    @foreach(range(1, $blocks ?? 2) as $i)
    <div class="skeleton-card" style="height:{{ $blockHeight ?? 200 }}px;margin-bottom:16px;"></div>
    @endforeach
</div>
