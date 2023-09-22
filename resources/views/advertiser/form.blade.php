@extends('layouts.app')

@section('content')


<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Add New Advertizer Detail') }}</div>

                <div class="card-body">
                    
                    <!-- START Advertizer Form for adding new request--> 
                    
                   <form method="POST" action="{{ route('advertiser.formsave') }}">
                        @csrf

                        <div class="row mb-3">
                            <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('Advertizer Name') }}</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>

                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <label for="manual_id" class="col-md-4 col-form-label text-md-end">{{ __('Advertizer Manual Id') }}</label>

                            <div class="col-md-6">
                                <input id="manual_id" type="text" class="form-control @error('manual_id') is-invalid @enderror" name="manual_id" value="{{ old('manual_id') }}" required autocomplete="name" autofocus>

                                @error('manual_id')
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
                    
                     <!-- END Advertizer Form for adding new request--> 
                    
                    
                </div>
            </div>
        </div>
    </div>
</div>



@endsection