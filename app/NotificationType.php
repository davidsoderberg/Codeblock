<?php namespace App;

class NotificationType{
	const MENTION = 'Mention';
	const REPLY = 'Reply';
	const COMMENT = 'Comment';
	const STAR = 'Star';
	const ROLE = 'Role';
	const BANNED = 'Banned';
	const FAVOURITE = 'Favourite';

	public static function isType($type){
		$type = strtoupper($type);
		$rc = new \ReflectionClass('App\NotificationType');
		return array_key_exists($type, $rc->getConstants());
	}
}