@extends('layouts.app')
@section('title') Login @endsection
@section('content')
<div class="card-header bg-img">
    <div class="bg-overlay"></div>
    <h3 class="text-center m-t-10 text-white"> Login</h3>
</div>
<div class="card-body">
  <form class="form-horizontal m-t-20" action="{{ route('login') }}" method="post">
      @csrf
      <div class="form-group">
          <div class="col-md-12">
            @error('banned')
                <p class="text-danger">{{ $message }}</p>
            @enderror
          </div>
          <div class="col-12"> 
              <input id="username" type="text" class="form-control input-lg @error('username') is-invalid @enderror" name="username" autocomplete="false" required autofocus>
              @error('username')
                  <span class="invalid-feedback" role="alert">
                      <strong>{{ $message }}</strong>
                  </span>
              @enderror
          </div>
 

      </div>
      <div class="form-group">
          <div class="col-12">
              <input id="password" type="password" class="form-control input-lg @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
              @error('password')
                  <span class="invalid-feedback" role="alert">
                      <strong>{{ $message }}</strong>
                  </span>
              @enderror
          </div>
      </div>
      <div class="form-group">
          <div class="col-12">
              <div class="checkbox checkbox-primary">
                  <input id="checkbox-signup" type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                  <label for="checkbox-signup">
                      Remember me
                  </label>
              </div>

          </div>
      </div>

      <div class="form-group text-center m-t-40">
          <div class="col-12">
              <button class="btn btn-primary btn-lg w-lg waves-effect waves-light" type="submit">Log In</button>
          </div>
      </div>

      <div class="form-group row m-t-30">
          <div class="col-sm-7">
              <!-- <a href="{{ route('password.request') }}"><i class="fa fa-lock m-r-5"></i> Forgot your password?</a> -->
          </div>
      </div>
  </form>
</div>

<script>
    
    
</script>

@endsection
