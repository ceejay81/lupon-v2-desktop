<div style="display: flex; flex-direction: column; gap: 1rem;">

    <div>
        <label class="form-label">Full Name <span style="color: var(--danger);">*</span></label>
        <input type="text" name="name" value="{{ old('name', $citizen->name ?? '') }}"
            class="form-control" placeholder="e.g. Juan Dela Cruz">
        @error('name')
            <p class="form-error"><i class="ph ph-warning-circle"></i> {{ $message }}</p>
        @enderror
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
        <div>
            <label class="form-label">Purok</label>
            <input type="text" name="purok" value="{{ old('purok', $citizen->purok ?? '') }}"
                class="form-control" placeholder="e.g. Purok 3">
            @error('purok')
                <p class="form-error"><i class="ph ph-warning-circle"></i> {{ $message }}</p>
            @enderror
        </div>
        <div>
            <label class="form-label">Phone Number</label>
            <input type="tel" name="phone" value="{{ old('phone', $citizen->phone ?? '') }}"
                maxlength="11"
                class="form-control" placeholder="09XX XXX XXXX">
            @error('phone')
                <p class="form-error"><i class="ph ph-warning-circle"></i> {{ $message }}</p>
            @enderror
        </div>
    </div>

    <div>
        <label class="form-label">Address</label>
        <input type="text" name="address" value="{{ old('address', $citizen->address ?? '') }}"
            class="form-control" placeholder="Street, Barangay Bula">
        @error('address')
            <p class="form-error"><i class="ph ph-warning-circle"></i> {{ $message }}</p>
        @enderror
    </div>

</div>
