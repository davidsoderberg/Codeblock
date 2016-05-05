<?php namespace integration;

use Illuminate\Support\Facades\Auth;

class PostControllerTest extends \IntegrationCase {

	public function setUp() {
		parent::setUp();
		$this->setUpDb();
		Auth::loginUsingId( 1 );
	}

	public function test_view_posts() {
		$this->visit( 'posts' )->seeStatusCode( 200 );
	}

	public function create_post() {
		$this->visit( 'posts/create' )->submitForm( 'Create', [
			"description" => 'test codeblock',
			"code" => '<?php echo "hej";',
			'cat_id' => 2,
			"name" => 'test codeblock',
			'tags' => [1, 2],
		] )->see( 'Your block has been created.' );

		return $this;
	}

	public function test_create_post() {
		$this->create_post()->seePageIs( 'posts/1' );
	}

	public function test_view_with_tag() {
		$this->create_post();
		$this->visit( 'tags/list/1' )->see( 'test codeblock' );
	}

	public function test_view_with_category() {
		$this->create_post();
		$this->visit( 'categories/list/2' )->see( 'test codeblock' );
	}

	public function test_delete_post() {
		$this->create_post();
		$this->visit( '/' )->visit( 'posts/delete/1' )->see( 'Your codeblock has been deleted.' );
	}

	public function test_edit_post() {
		$this->create_post();
		$this->visit( 'posts/edit/1' )
		     ->submitForm( 'Save', ['description' => 'test'] )
		     ->see( 'Your block has been saved.' )
		     ->seePageIs( 'posts/1' );
	}

	public function test_star_post() {
		$this->create( 'App\Models\Post', ['user_id' => 2] );
		$this->visit('/')->visit( 'posts/star/1' )
		     ->see( 'You have now add a star to this codblock.' )
		     ->visit( 'posts/star/1' )
		     ->see( 'You have now removed a star from this codblock.' );
	}

	public function test_fork_post() {
		$this->create( 'App\Models\Post', ['user_id' => 2] );
		$this->visit( 'posts/fork/1' )->see( 'Your have forked a block and can now edit.' )->seePageIs( '/posts/edit/2' );
	}

	public function test_view_forked() {
		$this->test_fork_post();
		$this->visit( 'posts/forked/1' )->see( 'Forked codeblock from:' );
	}

	public function test_fork_from_github() {
		$id = 11315085;
		$this->visit( 'posts/create' )
		     ->submitForm( 'Fork gist', ['id' => $id] )
		     ->see( 'The requested <a href="https://gist.github.com/' . $id . '" target="_blank">gist</a> have been forked.' )
		     ->seePageIs( 'posts/1' );
	}
}