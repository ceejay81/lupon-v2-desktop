{{-- Case Nature & Description --}}
<div class="info-card">
    <div class="info-card-header">
        <h2 class="info-card-title">
            <i class="ph ph-info"></i>
            Case Information
        </h2>
    </div>
    <div class="info-card-body">
        <div class="case-nature">
            <span class="nature-label">Nature of Case</span>
            <span class="nature-value">{{ $case->nature_of_case }}</span>
        </div>

        @if($case->description)
            <div class="case-description">
                <h3 class="description-title">Case Description</h3>
                <p class="description-text">{{ $case->description }}</p>
            </div>
        @endif
    </div>
</div>

{{-- Blotter Information --}}
<div class="info-card">
    <div class="info-card-header">
        <h2 class="info-card-title">
            <i class="ph ph-notebook"></i>
            Blotter Details
        </h2>
    </div>
    <div class="info-card-body">
        <div class="blotter-grid" style="grid-template-columns: 1fr; justify-items: center; text-align: center;">
            <div class="blotter-item">
                <span class="blotter-label">Date Filed</span>
                <span class="blotter-value">{{ $case->filed_date->format('M d, Y') }}</span>
            </div>
            
            <div class="blotter-item">
                <span class="blotter-label">1st Hearing Date</span>
                <span class="blotter-value">{{ $case->date_of_first_hearing?->format('M d, Y') ?? '—' }}</span>
            </div>
            

        </div>
        
        <div class="blotter-status-grid">
            <div class="status-item">
                <span class="status-label">Amicably Settled</span>
                <span class="status-indicator {{ $case->amicably_settled ? 'yes' : 'no' }}">
                    {{ $case->amicably_settled ? 'Yes' : 'No' }}
                </span>
            </div>
            
            <div class="status-item">
                <span class="status-label">Mediation</span>
                <span class="status-indicator {{ $case->mediation ? 'yes' : 'no' }}">
                    {{ $case->mediation ? 'Yes' : 'No' }}
                </span>
            </div>
            
            <div class="status-item">
                <span class="status-label">Conciliation</span>
                <span class="status-indicator {{ $case->conciliation ? 'yes' : 'no' }}">
                    {{ $case->conciliation ? 'Yes' : 'No' }}
                </span>
            </div>
        </div>
        
        @if($case->remarks)
            <div class="blotter-remarks">
                <span class="blotter-label">Remarks</span>
                <p class="blotter-remarks-text">{{ $case->remarks }}</p>
            </div>
        @endif
    </div>
</div>
