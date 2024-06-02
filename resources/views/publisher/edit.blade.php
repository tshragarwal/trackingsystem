@extends('layouts.app')

@section('content')


<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Publisher Detail') }}</div>

                <div class="card-body">
                     @if(!empty($error))
                            <div class="alert alert-error" role="alert">
                                <h4 class="alert-heading">{{$error}}</h4>
                                
                            </div>
                     @elseif(!empty($data))
                    <!--  Advertiser Detail Form --> 
                   
                        <form method="POST" action="{{ route('publisher.update', ['company_id' => $companyID, 'id' => $data->id ]) }}">
                            @csrf
                            @method('patch')                           
                            @if (session('success_status'))
                              <h6 class="alert alert-success">{{ session('success_status') }}</h6>
                           @endif
                            @if (session('error_status'))
                              <h6 class="alert alert-danger">{{ session('error_status') }}</h6>
                           @endif
                           
                             
                             <div class="row mb-3">
                                 <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('Publisher Name') }}</label>
                                 
                                 <div class="col-md-6">
                                     <input id="name" type="text" class="form-control @error('name') is-invalid @enderror"  name="name" value="{{ $data->name }}" required autocomplete="name" autofocus>

                                     @error('name')
                                         <span class="invalid-feedback" role="alert">
                                             <strong>{{ $message }}</strong>
                                         </span>
                                     @enderror
                                 </div>
                             </div>

                             <div class="row mb-3">
                                 <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Publisher Email') }}</label>

                                 <div class="col-md-6">
                                     <input id="email" type="text" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $data->email }}" required>

                                     @error('email')
                                         <span class="invalid-feedback" role="alert">
                                             <strong>{{ $message }}</strong>
                                         </span>
                                     @enderror
                                 </div>
                             </div>

                             
                              <div class="row mb-3">
                                 <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('New Password') }}</label>

                                 <div class="col-md-6">
                                     <input id="password" type="text" class="form-control @error('password') is-invalid @enderror" name="password" value="" >

                                     @error('password')
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
                    @endif
                    
                     <!-- END Advertiser Form for adding new request--> 
                    
                    
                </div>
            </div>
        </div>
    </div>
</div>



@endsection