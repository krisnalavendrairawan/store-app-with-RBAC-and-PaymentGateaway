<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Purple Admin</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href={{ asset('vendors/mdi/css/materialdesignicons.min.css') }}>
    <link rel="stylesheet" href={{ asset('vendors/css/vendor.bundle.base.css') }}>
    <link rel="stylesheet" href={{ asset('css/style.css') }}>
    <!-- End layout styles -->
    <link rel="shortcut icon" href={{ asset('images/favicon.ico') }} />
  </head>
  <body>
    <div class="container-scroller">
      <div class="container-fluid page-body-wrapper full-page-wrapper">
        <div class="content-wrapper d-flex align-items-center auth">
          <div class="row flex-grow">
            <div class="col-lg-4 mx-auto">
              <div class="auth-form-light text-left p-5">
                <div class="brand-logo">
                  <img src={{ asset('images/logo.svg') }}>
                </div>
                <h4>Hello! let's get started</h4>

                @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

              <form class="pt-3" method="POST" action="{{ route('login') }}">
                  @csrf
                  <div class="form-group">
                  <input type="text" class="form-control form-control-lg" id="exampleInputEmail1" name="login" placeholder="Username or Phone">
                  </div>
                  <div class="form-group">
                      <input type="password" class="form-control form-control-lg" id="exampleInputPassword1" name="password" placeholder="Password">
                  </div>
                  <button type="submit" class="btn btn-block btn-gradient-primary btn-lg font-weight-medium auth-form-btn">SIGN IN</button>
                  <div class="my-2 d-flex justify-content-between align-items-center">
                      <div class="form-check">
                          <label class="form-check-label text-muted">
                              <input type="checkbox" class="form-check-input"> Keep me signed in 
                          </label>
                      </div>
                  </div>
              </form>


                
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <script src={{ asset('vendors/js/vendor.bundle.base.js') }}></script>
    <script src="{{ asset('js/off-canvas.js') }}"></script>
    <script src="{{ asset('js/hoverable-collapse.js') }}"></script>
    <script src="{{ asset('js/misc.js') }}"></script>
    <!-- endinject -->
  </body>
</html>