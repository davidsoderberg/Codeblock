<?php namespace integration;

use Illuminate\Support\Facades\Auth;

class PostControllerTest extends \IntegrationCase {

	public function setUp()
	{
		parent::setUp();
		$this->setUpDb();
		Auth::loginUsingId(1);
	}

	public function create_post(){
		$this->visit('posts/create')
			->submitForm('Create', ["description" => 'test codeblock', "code" => '<?php echo "hej";', 'category' => 2, "name" => 'test codeblock', 'tags' => [1,2]])
			->see('Your block has been created.');
		return $this;
	}

	public function test_create_post(){
		$this->create_post()->onPage('posts/1');
	}

	public function test_view_with_tag(){
		$this->create_post();
		$this->visit('posts/tag/1')
			->see('test codeblock');
	}

	public function test_view_with_category(){
		$this->create_post();
		$this->visit('posts/category/2')
			->see('test codeblock');
	}

	public function test_delete_post(){
		$this->create_post();
		$this
			->visit('/')
			->visit('posts/delete/1')
			->see('Your codeblock has been deleted.');
	}

	public function test_edit_post(){
		$this->create_post();
		$this->visit('posts/edit/1')
			->fill('test', 'description')
			->press('Save')
			->see('Your block has been saved.')
			->onPage('posts/1');
	}

	public function test_star_post(){
		$this->create('App\Post', ['user_id' => 2]);
		$this->visit('posts/star/1')
			->see('You have now add a star to this codblock.')
			->visit('posts/star/1')
			->see('You have now removed a star from this codblock.');
	}

	public function test_fork_post(){
		$this->create('App\Post', ['user_id' => 2]);
		$this->visit('posts/fork/1')
			->see('Your have forked a block and can now edit.')
			->onPage('/posts/edit/2');
	}

	public function test_view_forked(){
		$this->test_fork_post();
		$this->visit('posts/forked/1')
			->see('Forked codeblock from:');
	}

	public function test_fork_from_github(){
		$id = 11315085;
		$this->visit('posts/create')
			->submitForm('Fork gist', ['id' => $id])
		->see('The requested <a href="https://gist.github.com/' . $id . '" target="_blank">gist</a> have been forked.')
		->onPage('posts/1');
	}
}