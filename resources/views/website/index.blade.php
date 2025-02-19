@extends('layouts.website')
@section('title') ABC @endsection
@section('content')


<!--====================  hero slider area ====================-->
<div class="hero-slider-area space__bottom--r40">
    <div class="hero-slick-slider-wrapper">
      @foreach($getBanner as $banner)
        <div class="single-hero-slider single-hero-slider--background single-hero-slider--overlay position-relative bg-img" data-bg="{{ asset('uploads/banner/'.$banner->ban_image) }}">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <!-- hero slider content -->
                        <div class="hero-slider-content hero-slider-content--extra-space">
                            <h3 class="hero-slider-content__subtitle">{{ $banner->ban_subtitle }}</h3>
                            <h2 class="hero-slider-content__title space__bottom--50">{{ $banner->ban_title }}</h2>
                            <a href="#" class="default-btn default-btn--hero-slider">{{ $banner->ban_caption }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
        <!-- banner-image-1634124222-jpg (Need to change this img from live database) -->
    </div>
</div>
 
{{-- <div class="comp-info">
    <h1>ASLOOB BEDAA CONTRACTING COMPANY</h1>
  </div> --}}
@endsection
