
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Edit Advertiser Detail') }}</div>
                    
                    <div class="card-body">

                        <!-- START Advertiser Form for adding new request-->

                        <form method="POST" action="{{ route('advertiser.update', ['company_id' => $companyID, 'id' => $advertiser['id']]) }}">
                            @csrf
                            @method('patch')
                            
                            <div class="row mb-3">
                                <label for="name"
                                    class="col-md-4 col-form-label text-md-end">{{ __('Advertiser Name') }}</label>

                                <div class="col-md-6">
                                    <input id="name" type="text"
                                        class="form-control @error('name') is-invalid @enderror" name="name"
                                        value="{{ $advertiser['name'] }}" required autocomplete="name"
                                        autofocus>

                                    @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="manual_email"
                                    class="col-md-4 col-form-label text-md-end">{{ __('Advertiser Email') }}</label>

                                <div class="col-md-6">
                                    <input id="manual_email" type="text"
                                        class="form-control @error('manual_email') is-invalid @enderror" name="manual_email"
                                        value="{{ $advertiser['manual_email'] }}" autocomplete="name"
                                        autofocus>

                                    @error('manual_email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>


                            <div class="row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Submit') }}
                                    </button>
                                </div>
                            </div>
                        </form>

                        <!-- END Advertiser Form for adding new request-->
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
