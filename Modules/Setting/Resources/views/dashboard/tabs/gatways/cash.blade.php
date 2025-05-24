<div class="row">
    <div class="col-md-7 col-md-offset-2">

        <div class="form-group">
            <label class="col-md-2">
                {{ __('setting::dashboard.settings.form.supported_countries') }}
            </label>
            <div class="col-md-9">
                <select name="payment_gateway[cash][supported_countries]" class="form-control select2" multiple=""
                    data-placeholder="{{ __('setting::dashboard.settings.form.all_countries') }}">
                    @foreach ($countries as $code => $country)
                        <option value="{{ $code }}" @if (collect(config('setting.payment_gateway.cash.supported_countries', []))->contains($code)) selected="" @endif>
                            {{ $country }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        {!! field()->checkBox(
            'payment_gateway[cash][status]',
            __('setting::dashboard.settings.form.payment_gateway.payment_types.payment_status'),
            null,
            [
                config('setting.payment_gateway.cash.status') == 'on' ? 'checked' : '' => '',
            ],
        ) !!}
    </div>
</div>
