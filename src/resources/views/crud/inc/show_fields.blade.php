{{-- Show the inputs --}}
@foreach ($fields as $field)
@php
    $key = ((array)$field['name'])[0].uniqid();
  \Debugbar::startMeasure('measureing-'.$key);
@endphp
    @include($crud->getFirstFieldView($field['type'], $field['view_namespace'] ?? false), $field)
    @php
  \Debugbar::stopMeasure('measureing-'.$key);
@endphp
@endforeach

