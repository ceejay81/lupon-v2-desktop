{{-- Parties Information --}}
<div class="parties-card">
    <div class="info-card-header">
        <h2 class="info-card-title">
            <i class="ph ph-users"></i>
            Parties Involved
        </h2>
    </div>
    <div class="parties-grid">
        {{-- Complainant --}}
        <div class="party-section complainant">
            <div class="party-header">
                <div class="party-icon complainant-icon">
                    <i class="ph ph-user-plus"></i>
                </div>
                <div class="party-label">
                    <span class="party-title">Complainant</span>
                    <span class="party-subtitle">Filing the complaint</span>
                </div>
            </div>
            <div class="party-details">
                <h4 class="party-name">
                    @if($case->complainant_id)
                        <a href="{{ route('citizens.show', $case->complainant_id) }}" style="color: inherit; text-decoration: none;">
                            {{ $case->complainant }}
                        </a>
                    @else
                        {{ $case->complainant }}
                    @endif
                </h4>
                <div class="party-info">
                    <div class="info-item">
                        <i class="ph ph-map-pin"></i>
                        <span>{{ $case->complainant_address ?: 'No address' }}</span>
                    </div>
                    @if($case->complainant_phone)
                    <div class="info-item">
                        <i class="ph ph-phone"></i>
                        <span>{{ $case->complainant_phone }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- VS Divider --}}
        <div class="vs-divider">
            <span>VS</span>
        </div>

        {{-- Respondent --}}
        <div class="party-section respondent">
            <div class="party-header">
                <div class="party-icon respondent-icon">
                    <i class="ph ph-user-minus"></i>
                </div>
                <div class="party-label">
                    <span class="party-title">Respondent</span>
                    <span class="party-subtitle">Responding to complaint</span>
                </div>
            </div>
            <div class="party-details">
                <h4 class="party-name">
                    @if($case->respondent_id)
                        <a href="{{ route('citizens.show', $case->respondent_id) }}" style="color: inherit; text-decoration: none;">
                            {{ $case->respondent }}
                        </a>
                    @else
                        {{ $case->respondent }}
                    @endif
                </h4>
                <div class="party-info">
                    <div class="info-item">
                        <i class="ph ph-map-pin"></i>
                        <span>{{ $case->respondent_address ?: 'No address' }}</span>
                    </div>
                    @if($case->respondent_phone)
                    <div class="info-item">
                        <i class="ph ph-phone"></i>
                        <span>{{ $case->respondent_phone }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
