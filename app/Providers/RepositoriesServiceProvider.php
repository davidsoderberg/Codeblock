<?php namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoriesServiceProvider extends ServiceProvider {

	// registerar alla repon denna funktionen binder repot till rätt version av repot.
	public function register() {
		$this->app->bind('App\Repositories\User\UserRepository', 'App\Repositories\User\EloquentUserRepository');
		$this->app->bind('App\Repositories\Post\PostRepository', 'App\Repositories\Post\EloquentPostRepository');
		$this->app->bind('App\Repositories\Tag\TagRepository', 'App\Repositories\Tag\EloquentTagRepository');
		$this->app->bind('App\Repositories\Category\CategoryRepository', 'App\Repositories\Category\EloquentCategoryRepository');
		$this->app->bind('App\Repositories\Comment\CommentRepository', 'App\Repositories\Comment\EloquentCommentRepository');
		$this->app->bind('App\Repositories\Permission\PermissionRepository', 'App\Repositories\Permission\EloquentPermissionRepository');
		$this->app->bind('App\Repositories\Role\RoleRepository', 'App\Repositories\Role\EloquentRoleRepository');
		$this->app->bind('App\Repositories\Rate\RateRepository', 'App\Repositories\Rate\EloquentRateRepository');
		$this->app->bind('App\Repositories\Notification\NotificationRepository', 'App\Repositories\Notification\EloquentNotificationRepository');
		$this->app->bind('App\Repositories\Forum\ForumRepository', 'App\Repositories\Forum\EloquentForumRepository');
		$this->app->bind('App\Repositories\Topic\TopicRepository', 'App\Repositories\Topic\EloquentTopicRepository');
		$this->app->bind('App\Repositories\Reply\ReplyRepository', 'App\Repositories\Reply\EloquentReplyRepository');
		$this->app->bind('App\Repositories\Read\ReadRepository', 'App\Repositories\Read\EloquentReadRepository');
	}

}