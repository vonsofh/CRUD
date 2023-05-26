@extends(backpack_view('blank'))

@php

@endphp

@section('header')
    <section class="header-operation container-fluid animated fadeIn d-flex mb-2 align-items-end d-print-none">
        <h3 class="text-capitalize mb-0" style="line-height: 30px;">{!! $crud->getOperationSetting('heading') !!}</h3>
        <p class="ms-2 ml-2 mb-0">{!! $crud->getOperationSetting('subheading') !!}.</p>
        @if ($crud->hasAccess('list'))
            <p class="mb-0 ms-2 ml-2">
                <small><a href="{{ url($crud->route) }}" class="d-print-none font-sm"><i class="la la-angle-double-{{ config('backpack.base.html_direction') == 'rtl' ? 'right' : 'left' }}"></i> {{ trans('backpack::crud.back_to_all') }} <span>{{ $crud->entity_name_plural }}</span></a></small>
            </p>
        @endif
    </section>
@endsection

@section('content')
@include('crud::form_container')
@endsection

