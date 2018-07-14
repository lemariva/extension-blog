<?php

namespace Pagekit\Blog\Controller;

use Pagekit\Application as App;
use Pagekit\Blog\Model\Comment;
use Pagekit\Blog\Model\Post;
use Pagekit\Blog\Model\Category;
use Pagekit\User\Model\Role;
use Pagekit\Blog\Helper\AnalyticsHelper;

/**
 * @Access(admin=true)
 */
class BlogController
{
    /**
     * @Access("blog: manage own categories || blog: manage all categories")
     * @Request({"filter": "array", "page":"int"})
     */
    public function categoryAction($filter = null, $page = null)
    {
        return [
            '$view' => [
                'title' => __('Categories'),
                'name' => 'blog/admin/category-index.php'
            ],
            '$data' => [
                'canEditAll' => App::user()->hasAccess('blog: manage all categories'),
                'config' => [
                    'filter' => (object)$filter,
                    'page' => $page
                ]
            ]
        ];
    }

    /**
     * @Route("/category/edit", name="category/edit")
     * @Access("blog: manage all categories")
     * @Request({"id": "int"})
     */
    public function editCategoryAction($id = 0)
    {
        try {
            if (!$category = Category::where(compact('id'))->first()) {
                if ($id) {
                    App::abort(404, __('Invalid post id.'));
                }

                $category = new Category();
            }

            $user = App::user();
            if (!$user->hasAccess('blog: manage all categories') && $post->user_id !== $user->id) {
                App::abort(403, __('Insufficient User Rights.'));
            }

            return [
                '$view' => [
                    'title' => $id ? __('Edit Category') : __('Add Category'),
                    'name' => 'blog/admin/category-edit.php'
                ],
                '$data' => [
                    'category' => $category
                ],
                'category' => $category
            ];
        } catch (\Exception $e) {

            App::message()->error($e->getMessage());

            return App::redirect('@blog/category');
        }
    }

    /**
     * @Route("/gapi/update", name="/gapi/update", methods="POST")
     * @Access("blog: manage own posts || blog: manage all posts")
     */
    public function updateVisitsAction()
    {
        $query  = Post::query();
        $config = App::module('blog')->config();

        $gAnalytics = new AnalyticsHelper();

        // user without universal access can not edit this
        if(!App::user()->hasAccess('blog: manage all posts')) {
            App::abort(403, __('Insufficient User Rights.'));
        }

        if(!$config['gapi']['gapi_enabled']){
            App::abort(400, __('Access denied. Enable and configure Gapi in settings!'));
        }

        $count = $query->count();
        $posts = array_values($query->orderBy('date', 'desc')->get());

        foreach ($posts as $post)
        {
          $webpage = $post->slug;
          $starDate = $config['gapi']['start_date'];

          $starDateWeek = date("Y-m-d", strtotime("- 7 days")); 



          $analytic_report_visitors = $gAnalytics->apiAction($webpage, $starDate);
          $analytic_week_visitors = $gAnalytics->apiAction($webpage, $starDateWeek);

          if($analytic_report_visitors['message'] != 'ok')
          {
              return App::response()->json($analytic_report_visitors, 400);
          }

          if($analytic_week_visitors['message'] != 'ok')
          {
              return App::response()->json($analytic_week_visitors, 400);
          }


          $post->visitor_count = $analytic_report_visitors['visits'];
          $post->visitor_week_count = $analytic_week_visitors['visits'];
          $post->save();

        }
        return true;
    }


    /**
     * @Access("blog: manage own posts || blog: manage all posts")
     * @Request({"filter": "array", "page":"int"})
     */
    public function postAction($filter = null, $page = null)
    {
        return [
            '$view' => [
                'title' => __('Posts'),
                'name' => 'blog/admin/post-index.php'
            ],
            '$data' => [
                'statuses' => Post::getStatuses(),
                'authors' => Post::getAuthors(),
                'canEditAll' => App::user()->hasAccess('blog: manage all posts'),
                'config' => [
                    'filter' => (object)$filter,
                    'page' => $page
                ]
            ]
        ];
    }

    /**
     * @Route("/post/edit", name="post/edit")
     * @Access("blog: manage own posts || blog: manage all posts")
     * @Request({"id": "int"})
     */
    public function editAction($id = 0)
    {
        try {

            if (!$post = Post::where(compact('id'))->related('user')->first()) {

                if ($id) {
                    App::abort(404, __('Invalid post id.'));
                }

                $module = App::module('blog');

                $post = Post::create([
                    'user_id' => App::user()->id,
                    'status' => Post::STATUS_DRAFT,
                    'date' => new \DateTime(),
                    'comment_status' => (bool)$module->config('posts.comments_enabled')
                ]);

                $post->set('title', $module->config('posts.show_title'));
                $post->set('markdown', $module->config('posts.markdown_enabled'));
            }

            $post->tags = implode($post->getTags(), ';');

            $user = App::user();
            if (!$user->hasAccess('blog: manage all posts') && $post->user_id !== $user->id) {
                App::abort(403, __('Insufficient User Rights.'));
            }

            $roles = App::db()->createQueryBuilder()
                ->from('@system_role')
                ->where(['id' => Role::ROLE_ADMINISTRATOR])
                ->whereInSet('permissions', ['blog: manage all posts', 'blog: manage own posts'], false, 'OR')
                ->execute('id')
                ->fetchAll(\PDO::FETCH_COLUMN);

            $authors = App::db()->createQueryBuilder()
                ->from('@system_user')
                ->whereInSet('roles', $roles)
                ->execute('id, username')
                ->fetchAll();

            $categories = Category::query()->get();

            return [
                '$view' => [
                    'title' => $id ? __('Edit Post') : __('Add Post'),
                    'name' => 'blog/admin/post-edit.php'
                ],
                '$data' => [
                    'post' => $post,
                    'statuses' => Post::getStatuses(),
                    'roles' => array_values(Role::findAll()),
                    'canEditAll' => $user->hasAccess('blog: manage all posts'),
                    'authors' => $authors,
                    'categories' => $categories
                ],
                'post' => $post
            ];

        } catch (\Exception $e) {

            App::message()->error($e->getMessage());

            return App::redirect('@blog/post');
        }
    }

    /**
     * @Access("blog: manage comments")
     * @Request({"filter": "array", "post":"int", "page":"int"})
     */
    public function commentAction($filter = [], $post = 0, $page = null)
    {
        $post = Post::find($post);
        $filter['order'] = 'created DESC';

        return [
            '$view' => [
                'title' => $post ? __('Comments on %title%', ['%title%' => $post->title]) : __('Comments'),
                'name' => 'blog/admin/comment-index.php'
            ],
            '$data' => [
                'statuses' => Comment::getStatuses(),
                'config' => [
                    'filter' => (object)$filter,
                    'page' => $page,
                    'post' => $post,
                    'limit' => App::module('blog')->config('comments.comments_per_page')
                ]
            ]
        ];
    }

    /**
     * @Access("system: access settings")
     */
    public function settingsAction()
    {
        return [
            '$view' => [
                'title' => __('Blog Settings'),
                'name' => 'blog/admin/settings.php'
            ],
            '$data' => [
                'config' => App::module('blog')->config()
            ]
        ];
    }
}
