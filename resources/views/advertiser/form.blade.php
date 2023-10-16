@extends('layouts.app')

@section('content')


<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                @if(!empty($advertizer))
                    <div class="card-header">{{ __('Edit Advertizer Detail') }}</div>
                @else
                    <div class="card-header">{{ __('Add New Advertizer Detail') }}</div>
                @endif

                <div class="card-body">
                    
                    <!-- START Advertizer Form for adding new request--> 
                    
                   <form method="POST" action="{{ route('advertiser.formsave') }}">
                        @csrf
                        <input type="hidden" name="advertizer_id" value="{{$advertizer['id']??0}}" />
                        <div class="row mb-3">
                            <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('Advertizer Name') }}</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror"  name="name" value="{{ $advertizer['name']??old('name') }}" required autocomplete="name" autofocus>

                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <label for="manual_email" class="col-md-4 col-form-label text-md-end">{{ __('Advertizer Email') }}</label>

                            <div class="col-md-6">
                                <input id="manual_email" type="text" class="form-control @error('manual_email') is-invalid @enderror" name="manual_email" value="{{ $advertizer['manual_email']??old('manual_email') }}"  autocomplete="name" autofocus>

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
                    
                     <!-- END Advertizer Form for adding new request--> 
                    
                    
                </div>
            </div>
        </div>
    </div>
</div>



@endsection