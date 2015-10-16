<!DOCTYPE html>
<html lang="en-US">
<head>
	<meta charset="utf-8">
	<title>{{$subject}}</title>
</head>
<body>
<h2>{{$subject}} to {{$team->name}}</h2>
<p>Hi {{$user->username}},<br />{{Auth::user()->username}} has invited you to {{$team->name}}, accept or deny this invite below.</p>
<p>{{link_to_action('TeamController@respondInvite', 'Accept', ['token' => $invite->accept_token], ['target' => '_blank'])}} - {{link_to_action('TeamController@respondInvite', 'Deny', ['token' => $invite->deny_token], ['target' => '_blank'])}}</p>
<p>From/<br /> David Southmountian</p>
</body>
</html>