{{ trans('backpack::base.click_here_to_reset') }}: <a href="{{ $link = backpack_url('password/reset', $token).'?email='.urlencode($user->{ backpack_email_column() }) }}"> {{ $link }} </a>
