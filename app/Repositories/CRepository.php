<?php namespace App\Repositories;

use App\Services\CacheTrait;
use Illuminate\Support\Facades\Mail;

// bas klassen för repon
/**
 * Class CRepository
 * @package App\Repositories
 */
Class CRepository {

	use CacheTrait;

	/**
	 * Property to store errors in.
	 *
	 * @var
	 */
	public $errors;

	/**
	 * Getter for errors property.
	 *
	 * @return mixed
	 */
	public function getErrors() {
		return $this->errors;
	}

	/**
	 * Sending all mails from this application.
	 *
	 * @param $template
	 * @param $emailInfo
	 * @param $data
	 *
	 * @return bool
	 */
	public function sendEmail( $template, $emailInfo, $data ) {
		Mail::send( $template, $data, function ( $message ) use ( $emailInfo ) {
			$message->from( env( 'FROM_ADRESS' ), env( 'FROM_NAME' ) );
			$message->to( $emailInfo['toEmail'], $emailInfo['toName'] )->subject( $emailInfo['subject'] );
		} );

		if ( count( Mail::failures() ) <= 0 ) {
			return true;
		}
	}

	/**
	 * Secures input from user.
	 *
	 * @param $input
	 *
	 * @return string
	 */
	public function stripTrim( $input ) {
		return trim( strip_tags( $input ) );
	}

	/**
	 * Checks if array is an assoc array.
	 *
	 * @param $array
	 *
	 * @return bool
	 */
	public function is_assoc( $array ) {
		foreach( array_keys( $array ) as $key ) {
			if ( !is_int( $key ) ) {
				return true;
			}
		}

		return false;
	}
}