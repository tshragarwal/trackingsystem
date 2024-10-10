@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Update Publisher Job') }}</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('publisherJob.update', ['company_id' => $companyID, 'id' => $data->id]) }}">
                            @csrf
                            @method('patch')                           

                            @if (session('success_status'))
                                <h6 class="alert alert-success">{{ session('success_status') }}</h6>
                            @endif

                            @if (session('error_status'))
                                <h6 class="alert alert-danger">{{ session('error_status') }}</h6>
                            @endif


                            <div class="row mb-3">
                                <label for="advertiser_id"
                                    class="col-md-4 col-form-label text-md-end">{{ __('Advertiser') }}</label>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input id="advertiser_name" type="text" class="form-control @error('name') is-invalid @enderror"  name="advertiser_name" value="{{ $data->campaign->advertiser->name }}" required autocomplete="name" autofocus readonly>
                                        <input id="advertiser_id" type="hidden" class="form-control @error('name') is-invalid @enderror"  name="advertiser_id" value="{{ $data->campaign->advertiser->id }}" required autocomplete="name" autofocus>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="advertiser_campaign_id"
                                    class="col-md-4 col-form-label text-md-end">{{ __('Campaign') }}</label>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input id="advertiser_campaign_name" type="text" class="form-control @error('name') is-invalid @enderror"  name="advertiser_campaign_name" value="{{ $data->campaign->campaign_name }}" required autocomplete="name" autofocus readonly>
                                        <input id="advertiser_campaign_id" type="hidden" class="form-control @error('name') is-invalid @enderror"  name="advertiser_campaign_id" value="{{ $data->campaign->id }}" required autocomplete="name" autofocus>
                                    </div>

                                    @error('advertiser_campaign_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>


                            <div class="row mb-3">
                                <label for="publisher_id"
                                    class="col-md-4 col-form-label text-md-end">{{ __('Publisher') }}</label>


                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input id="publisher_name" type="text" class="form-control @error('name') is-invalid @enderror"  name="publisher_name" value="{{ $data->publisher->name }}" required autocomplete="name" autofocus readonly>
                                        <input id="publisher_id" type="hidden" class="form-control @error('name') is-invalid @enderror"  name="publisher_id" value="{{ $data->publisher->id }}" required autocomplete="name" autofocus>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="fallback_url"
                                    class="col-md-4 col-form-label text-md-end">{{ __('Fallback URL') }}</label>

                                <div class="col-md-6">
                                    <input id="fallback_url" type="text"
                                        class="form-control @error('fallback_url') is-invalid @enderror"
                                        name="fallback_url" value="{{ $data->fallback_url }}" autocomplete="email">

                                    @error('fallback_url')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="target_count"
                                    class="col-md-4 col-form-label text-md-end">{{ __('Target Count') }}</label>

                                <div class="col-md-6">
                                    <input id="target_count" type="number"
                                        class="form-control @error('target_count') is-invalid @enderror"
                                        name="target_count" value="{{ $data->target_count }}" autocomplete="email">

                                    @error('target_count')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="tracking_count"
                                    class="col-md-4 col-form-label text-md-end">{{ __('Daily track Count') }}</label>

                                <div class="col-md-6">
                                    <input id="tracking_count" type="number"
                                        class="form-control @error('target_count') is-invalid @enderror"
                                        name="tracking_count" value="{{ $data->tracking_count }}" autocomplete="email">

                                    @error('tracking_count')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Update') }}
                                    </button>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
