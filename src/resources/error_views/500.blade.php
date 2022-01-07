@extends('errors.layout')

@php
	$error_number = 500;
	$title = "It's not you, it's me.";
	$description = "An internal server error has occurred. If the error persists please contact the development team.";

	if (isset($exception)) {
		// if a message exists in the error, show that
		if ($exception->getMessage()) {
			$description = $exception->getMessage();
		}
		// if title and description have been given as headers, show those
		$title = $exception->getHeaders()['title'] ?? $title;
		$description = $exception->getHeaders()['description'] ?? $description;
		$details = $exception->getHeaders()['details'] ?? false;
		$link = $exception->getHeaders()['link'] ?? false;
	}
@endphp

@section('title')
	{!! $title !!}
@endsection

@section('description')
	<span class="text-danger">{!! $description !!}</span>

	@if ($details || $link)
	<div class="col-md-10 offset-1">
		@if ($details)
			<p class="mt-5"><small>{!! $details !!}</small></p>
		@endif

		@if ($link)
			<a target="_blank" href="{{ $link }}" class="btn btn-default"><i class="la la-external-link-alt"></i> Read more</a>
		@endif
	</div>
	@endif

@endsection
